<?php
declare(strict_types=1);

namespace App\Lib\Records;

class FieldsCollection implements IntfFieldsCollection {

    function __construct() {
        $this->values = new Utils\FieldsMap();
    }

    public function initializeField(string $fieldName, mixed $fieldValue) {
        $this->values->setValue($fieldName, $fieldValue);
    }

    public function tryGetFieldValue(string $fieldName, mixed &$fieldValue): bool {
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
        return $this->values->keyExists($fieldName);
    }

    private $values;
}
?>