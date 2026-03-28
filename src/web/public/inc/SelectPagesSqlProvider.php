<?php
declare(strict_types=1);

namespace SelectPages;

interface ISelectPagesSqlProvider {
    public function selectStmContainsTotalRows(): bool;

    public function getSelectStmInPages(
        string $selectSql,
        string $offsetParamName,
        string $pageSizeParamName,
        string $totalRowsFieldName
    ): string;

    public function getTotalRowsSelectStm(
        string $selectSql
    ): string;
}