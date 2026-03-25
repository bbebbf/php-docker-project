<?php
declare(strict_types=1);

namespace SelectPages;

class SelectPagesResult {
    public bool $error = false;
    public array $data = [];
    public int $currentPage = 0;
    public int $totalPages = 0;
}

class SelectPages {

    private const OFFSET_PARAM_NAME = ':SelectPagesOffset';
    private const PAGE_SIZE_PARAM_NAME = ':SelectPagesPageSize';
    private const TOTAL_ROWS_FIELD_NAME = 'SelectPagesTotalRows';

    private ?\PDOStatement $stmt = null;

    public function __construct(
        private \PDO $db,
        private ISelectPagesSqlProvider $selectPagesSqlProvider,
        private string $selectSql,
        private string $orderBy,
        private int $pageSize,
    ) {}

    public function __destruct() {
        $this->stmt = null;
    }

    public function fetch(int $pageNo, array $bindValues = []): SelectPagesResult {
        if ($this->stmt === null) {
            $this->stmt = $this->db->prepare($this->getSelectStmInPages());
        }

        foreach ($bindValues as $key => $value) {
            $this->stmt->bindValue($key, $value);
        }
        $offset = ($pageNo - 1) * $this->pageSize;
        $this->stmt->bindValue(self::OFFSET_PARAM_NAME, $offset, \PDO::PARAM_INT);
        $this->stmt->bindValue(self::PAGE_SIZE_PARAM_NAME, $this->pageSize, \PDO::PARAM_INT);

        $result = new SelectPagesResult();
        $result->currentPage = $pageNo;

        if ($this->stmt->execute() === false) {
            $result->error = true;
        }
        else {
            $stmtResult = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
            $this->stmt->closeCursor();

            $queryTotalRows = true;
            $totalRowCount = 0;

            if (count($stmtResult) === 0) {
                $queryTotalRows = ($pageNo > 1);
                $result->data = [];
            }
            else {
                if ($this->selectPagesSqlProvider->selectStmContainsTotalRows() === true) {
                    $queryTotalRows = false;
                    $totalRowCount = (int)$stmtResult[0][self::TOTAL_ROWS_FIELD_NAME];
                    $result->data = array_map(function ($item) {
                        unset($item[self::TOTAL_ROWS_FIELD_NAME]);
                        return $item;
                    }, $stmtResult);
                }
                else {
                    $queryTotalRows = true;
                    $result->data = $stmtResult;
                }
            }

            if ($queryTotalRows === true) {
                $totalRowsStmt = $this->db->prepare($this->getTotalRowsSelectStm());
                foreach ($bindValues as $key => $value) {
                    $totalRowsStmt->bindValue($key, $value);
                }
                if ($totalRowsStmt->execute() === false) {
                    $result->error = true;
                    return $result;
                }
                $totalRowCount = (int)$totalRowsStmt->fetch(\PDO::FETCH_NUM)[0];
                $this->stmt->closeCursor();
            }
            $result->totalPages = $this->getTotalPages($totalRowCount);
        }
        return $result;
    }

    private function getTotalPages(int $totalRows): int {
        return (int)ceil($totalRows / $this->pageSize);
    }

    private function getSelectStmInPages(): string {

        $result = $this->selectPagesSqlProvider->getSelectStmInPages(
            $this->selectSql,
            $this->orderBy,
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