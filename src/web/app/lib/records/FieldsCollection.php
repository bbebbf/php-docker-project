<?php
declare(strict_types=1);

namespace App\Lib\Records;

class FieldsCollection implements IntfFieldsCollection {

    public function initializeField(string $fieldName, mixed $fieldValue) {
        $this->createFieldsMap();
        $this->values->setValue($fieldName, $fieldValue);
    }

    public function tryGetFieldValue(string $fieldName, mixed &$fieldValue): bool {
        $this->createFieldsMap();
        return $this->values->tryGetValue($fieldName, $fieldValue);
    }

    public function getFieldValue(string $fieldName): mixed {
        $fieldValue = null;
        if ($this->tryGetFieldValue($fieldName, $fieldValue)) {
            return $fieldValue;
        }
        return null;
    }

    public function fieldExists(string $fieldName): bool {
        $this->createFieldsMap();
        return $this->values->keyExists($fieldName);
    }

    public function createFieldsMap() {
        if (is_null($this->values)) {
            $this->values = new Utils\FieldsMap();
        }
    }

    private $values = null;
}
?>