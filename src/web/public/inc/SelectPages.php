<?php
declare(strict_types=1);

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

    private ?PDOStatement $stmt = null;

    public function __construct(
        private PDO $db,
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
        $this->stmt->bindValue(self::OFFSET_PARAM_NAME, $offset, PDO::PARAM_INT);
        $this->stmt->bindValue(self::PAGE_SIZE_PARAM_NAME, $this->pageSize, PDO::PARAM_INT);

        $result = new SelectPagesResult();
        $result->currentPage = $pageNo;

        if ($this->stmt->execute() === false) {
            $result->error = true;
        }
        else {
            $stmtResult = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->stmt->closeCursor();

            if (count($stmtResult) === 0) {

                $totalRowsStmt = $this->db->prepare($this->getTotalRowsSelectStm());
                foreach ($bindValues as $key => $value) {
                    $totalRowsStmt->bindValue($key, $value);
                }
                if ($totalRowsStmt->execute() === false) {
                    $result->error = true;
                    return $result;
                }
                $totalRowsStmtResult = $totalRowsStmt->fetchAll(PDO::FETCH_ASSOC);
                $this->stmt->closeCursor();

                $result->totalPages = $this->getTotalPages((int)$totalRowsStmtResult[0][self::TOTAL_ROWS_FIELD_NAME]);
                $result->data = [];
            }
            else {
                $result->totalPages = $this->getTotalPages((int)$stmtResult[0][self::TOTAL_ROWS_FIELD_NAME]);
                $result->data = array_map(function ($item) {
                    unset($item[self::TOTAL_ROWS_FIELD_NAME]);
                    return $item;
                }, $stmtResult);
            }
        }
        return $result;
    }

    private function getTotalPages(int $totalRows): int {
        return (int)ceil($totalRows / $this->pageSize);
    }

    private function getSelectStmInPages(): string {

        $result = 'with Data_CTE as (' . $this->selectSql . ')'
            . ' select *, count(*) over() as ' . self::TOTAL_ROWS_FIELD_NAME
            . ' from Data_CTE'
            . ' order by ' . $this->orderBy
            . ' offset ' . self::OFFSET_PARAM_NAME . ' rows fetch next ' . self::PAGE_SIZE_PARAM_NAME . ' rows only';
        return $result;
    }

    private function getTotalRowsSelectStm(): string {

        $result = 'with Data_CTE as (' . $this->selectSql . ')'
            . ' select count(*) over() as ' . self::TOTAL_ROWS_FIELD_NAME
            . ' from Data_CTE';
        return $result;
    }
}