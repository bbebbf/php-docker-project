<?php
declare(strict_types=1);

namespace App\Lib\Records;

class EditableFieldsCollection implements IntfFieldsCollection, IntfEditableFieldsCollection {

    function __construct($intfFieldsCollection) {
        $this->intfFieldsCollection = $intfFieldsCollection;
        $this->edited_values = new Utils\FieldsMap();
    }

    public function initializeField(string $fieldName, $fieldValue) {
        // empty on purpose
    }

    public function getFieldValue(string $fieldName): mixed {
        $fieldValue = null;
        if ($this->tryGetFieldValue($fieldName, $fieldValue)) {
            return $fieldValue;
        }
        return null;
    }

    public function tryGetFieldValue(string $fieldName, mixed &$fieldValue): bool {
        if ($this->edited_values->tryGetValue($fieldName, $fieldValue)) {
            return true;
        }
        return $this->intfFieldsCollection->tryGetFieldValue($fieldName, $fieldValue);
    }

    public function fieldExists(string $fieldName): bool {
        return $this->intfFieldsCollection->fieldExists($fieldName);
    }

    public function editValue(string $fieldName, mixed $fieldValue): bool {
        if (!$this->intfFieldsCollection->tryGetFieldValue($fieldName, $oldFieldValue)) {
            return false;
        }
        if ($oldFieldValue === $fieldValue) {
            return $this->edited_values->deleteKey($fieldName);
        }
        if (is_null($fieldValue)) {
            $this->edited_values->setValue($fieldName, null);
            return true;
        }
        if (gettype($fieldValue) !== gettype($oldFieldValue)) {
            return false;
        }
        $this->edited_values->setValue($fieldName, $fieldValue);
        return true;
}

    public function resetValues() {
        $this->edited_values->clear();
    }

    public function updatesValues() {
        foreach ($this->edited_values->getKeys() as $key) {
            $this->intfFieldsCollection->initializeField($key, $this->edited_values->getValue($key));
        }
        $this->resetValues();
    }

    public function isEdited(): bool {
        return !$this->edited_values->isEmpty();
    }

    public function isValueEdited(string $fieldName): bool {
        return $this->edited_values->keyExists($fieldName);
    }

    private $intfFieldsCollection;
    private $edited_values;
}
?>