<?php
declare(strict_types=1);

namespace SelectPages;

class SelectPagesSqlProviderMsSqlServer implements ISelectPagesSqlProvider {

    public function selectStmContainsTotalRows(): bool {
        return true;
    }

    public function getSelectStmInPages(
        string $selectSql,
        string $offsetParamName,
        string $pageSizeParamName,
        string $totalRowsFieldName
    ): string {

        $orderBy = '1';
        $selectParts = explode(' order by ', $selectSql);
        if (count($selectParts) === 2) {
            $selectSql = $selectParts[0];
            $orderBy = $selectParts[1];
        }

        $result = 'with Data_CTE as (' . $selectSql . ')'
            . ' select *, count(*) over() as ' . $totalRowsFieldName
            . ' from Data_CTE'
            . ' order by ' . $orderBy
            . ' offset ' . $offsetParamName . ' rows fetch next ' . $pageSizeParamName . ' rows only';
        return $result;
    }

    public function getTotalRowsSelectStm(
        string $selectSql
    ): string {
        $result = 'with Data_CTE as (' . $selectSql . ')'
            . ' select top 1 count(*) over()'
            . ' from Data_CTE';
        return $result;
    }
}
