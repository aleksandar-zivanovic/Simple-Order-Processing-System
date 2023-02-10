<?php
require_once 'database.php';

class Pagination extends Database
{
    public array $allowedPerPageValues = [5, 10, 20, 50, "all"];

    public function totalRows(string $tableName, ?string $columnName = null, ?string $columnValue = null): int
    {
        $statement = "SELECT COUNT(id) FROM {$tableName}";
        if($columnName != null && $columnName != "lcstatus") $statement .= " WHERE {$columnName} = '{$columnValue}'";
        if($tableName === "loyalty_card" && $columnName === "ctype") {
            $statement = "SELECT COUNT(lc.id) FROM loyalty_card lc LEFT JOIN customers c ON lc.lccid = c.id WHERE c.{$columnName} = '{$columnValue}'";
        }

        if($columnName == "lcstatus") {
            if($columnValue == "true") $statement = "SELECT COUNT(c.id) FROM customers c INNER JOIN loyalty_card lc ON c.id = lc.lccid";
            if($columnValue == "false") $statement = "SELECT COUNT(c.id) FROM customers c LEFT JOIN loyalty_card lc ON c.id = lc.lccid WHERE lc.id IS NULL";
            }

        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        return $query->fetch(PDO::FETCH_COLUMN);
    }

    public function totalPages(int|string $rowsPerPage, string $tableName, ?string $columnName = null, ?string $columnValue = null): int
    {
        if($tableName === "loyaltycards") $tableName = "loyalty_card";
        if($rowsPerPage != "all" && is_numeric($rowsPerPage)) {
            // looking for persons or companies with a card
//            if($tableName === "loyalty_card" && $columnName === "ctype") $tableName = "customers";
            $totalRows = $this->totalRows($tableName, $columnName, $columnValue);
            $plusPage = $totalRows % $rowsPerPage > 0 ? 1 : 0;
            return $totalRows / $rowsPerPage + $plusPage;
        } else {
            return 1;
        }
    }

    public function rowsPerPage(int|string $limit, int $currentPageNumber): string
    {
        if($limit != "all") {
            $statement = " LIMIT {$limit}";
            $offset = $limit * $currentPageNumber - $limit;
            if($currentPageNumber > 1) $statement .= " OFFSET $offset";
            return $statement;
        } else {
            return "";
        }
    }

    public function checkAllowedValues(int|string $value): bool
    {
        return in_array($value, $this->allowedPerPageValues);
    }
}