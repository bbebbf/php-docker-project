<?php
declare(strict_types=1);

namespace App\Lib\Records;

interface IntfEditableFieldsCollection {
    public function editValue(string $fieldName, mixed $fieldValue): bool;
    public function resetValues();
    public function updatesValues();
    public function isEdited(): bool;
    public function isValueEdited(string $fieldName): bool;
}
?>