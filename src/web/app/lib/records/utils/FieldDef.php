<?php
namespace App\Lib\Records\Utils;

enum FielDefDataType {
    case String;
    case Integer;
    case Float;
}

enum FielDefKeyType {
    case NoKey;
    case Key;
    case AutoincKey;
}

class FieldDef {

    function __construct($fieldName, $dataType, $isRequired, $keyType = FielDefKeyType.NoKey) {
        $this->fieldName = $fieldName;
        $this->dataType = $dataType;
        $this->isRequired = $isRequired;
        $this->keyType = $keyType;
    }

    public function getFieldName() {
        return $this->fieldName;
    }

    public function getDataType() {
        return $this->dataType;
    }

    public function getIsRequired() {
        return $this->isRequired;
    }

    public function getKeyType() {
        return $this->keyType;
    }

    private fieldName;
    private dataType;
    private isRequired;
    private keyType;
}
?>