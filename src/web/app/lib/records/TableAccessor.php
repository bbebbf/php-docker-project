<?php
namespace App\Lib\Records;

class TableAccessor {

    function __construct() {
        $this->values = new Utils\FieldsMap();
    }

    private $values;
}
?>