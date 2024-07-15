<?php
declare(strict_types=1);

namespace App\Lib\Records;

interface IntfFieldsCollection {
    public function initializeField(string $fieldName, mixed $fieldValue);
    public function getFieldValue(string $fieldName): mixed;
    public function tryGetFieldValue(string $fieldName, mixed &$fieldValue): bool;
    public function fieldExists(string $fieldName): bool;
}
?>