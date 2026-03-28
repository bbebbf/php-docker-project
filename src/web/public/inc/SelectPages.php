<?php
declare(strict_types=1);

namespace SelectPages;

class SelectPagesResult {
    public function __construct(
        public readonly bool $error = false,
        public readonly array $rows = [],
        public readonly int $currentPage = 0,
        public readonly int $totalPages = 0,
    ) {}
}

class SelectPages {

    private const OFFSET_PARAM_NAME = 'SelectPagesOffset';
    private const PAGE_SIZE_PARAM_NAME = 'SelectPagesPageSize';
    private const TOTAL_ROWS_FIELD_NAME = 'SelectPagesTotalRows';

    private SqlParamsAccessor $sqlParamsAccessor;

    public function __construct(
        private \PDO $db,
        private ISelectPagesSqlProvider $selectPagesSqlProvider,
        private string $selectSql,
        private int $pageSize,
    ) {
        $this->sqlParamsAccessor = new SqlParamsAccessor();
    }

    public function getParamTypeByName(string $paramName): int {
        return $this->sqlParamsAccessor->getParamTypeByName($paramName);
    }

    public function setParamTypeByName(string $paramName, int $paramType): void {
        $this->sqlParamsAccessor->setParamTypeByName($paramName, $paramType);
    }

    public function getParamValueByName(string $paramName): mixed {
        return $this->sqlParamsAccessor->getParamValueByName($paramName);
    }

    public function setParamValueByName(string $paramName, mixed $paramValue): void {
        $this->sqlParamsAccessor->setParamValueByName($paramName, $paramValue);
    }

    public function fetch(int $pageNo): SelectPagesResult {

        $offset = ($pageNo - 1) * $this->pageSize;
        $this->setParamTypeByName(self::OFFSET_PARAM_NAME, \PDO::PARAM_INT);
        $this->setParamTypeByName(self::PAGE_SIZE_PARAM_NAME, \PDO::PARAM_INT);

        $this->setParamValueByName(self::OFFSET_PARAM_NAME, $offset);
        $this->setParamValueByName(self::PAGE_SIZE_PARAM_NAME, $this->pageSize);

        $sqlError = false;
        $sqlResultRows = [];
        $totalPages = 0;

        $stmt = $this->prepareSelectStm($this->getSelectStmInPages());
         if ($stmt->execute() === false) {
            $sqlError = true;
        }
        else {
            $stmtResult = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $queryTotalRows = true;
            $totalRowCount = 0;

            if (count($stmtResult) === 0) {
                $queryTotalRows = ($pageNo > 1);
            }
            else {
                if ($this->selectPagesSqlProvider->selectStmContainsTotalRows() === true) {
                    $queryTotalRows = false;
                    $totalRowCount = (int)$stmtResult[0][self::TOTAL_ROWS_FIELD_NAME];
                    $sqlResultRows = array_map(function ($item) {
                        unset($item[self::TOTAL_ROWS_FIELD_NAME]);
                        return $item;
                    }, $stmtResult);
                }
                else {
                    $queryTotalRows = true;
                    $sqlResultRows = $stmtResult;
                }
            }

            if ($queryTotalRows === true) {
                $totalRowsStmt = $this->prepareSelectStm($this->getTotalRowsSelectStm());
                if ($totalRowsStmt->execute() === false) {
                    $sqlError = true;
                }
                else {
                    $totalRowCount = (int)$totalRowsStmt->fetch(\PDO::FETCH_NUM)[0];
                    $totalRowsStmt->closeCursor();
                }
            }
            $totalPages = $this->getTotalPages($totalRowCount);
        }
        return new SelectPagesResult(
            $sqlError,
            $sqlResultRows,
            $pageNo,
            $totalPages,
        );
    }

    private function prepareSelectStm(string $selectStmtStr): \PDOStatement {
        $stmt = $this->db->prepare($this->sqlParamsAccessor->parse($selectStmtStr));
        for ($i = 1; $i <= $this->sqlParamsAccessor->getParamCount(); $i++) {
            $paramName = $this->sqlParamsAccessor->getParamNameByIndex($i);
            $paramType = $this->sqlParamsAccessor->getParamTypeByName($paramName);
            $paramValue = $this->sqlParamsAccessor->getParamValueByName($paramName);
            
            //echo 'i: ' . $i . '/ paramName: ' . $paramName . '/ paramValue: ' . $paramValue . '/ paramType: ' . $paramType . "\n";
            
            $stmt->bindValue($i, $paramValue, $paramType);
        }
        return $stmt;
    }

    private function getTotalPages(int $totalRows): int {
        return (int)ceil($totalRows / $this->pageSize);
    }

    private function getSelectStmInPages(): string {

        $result = $this->selectPagesSqlProvider->getSelectStmInPages(
            $this->selectSql,
            self::OFFSET_PARAM_NAME,
            self::PAGE_SIZE_PARAM_NAME,
            self::TOTAL_ROWS_FIELD_NAME
        );
        return $result;
    }

    private function getTotalRowsSelectStm(): string {

        $result = $this->selectPagesSqlProvider->getTotalRowsSelectStm($this->selectSql);
        return $result;
    }
}