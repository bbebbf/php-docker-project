<?php
declare(strict_types=1);

namespace App\Lib\Records\Tests;

use PHPUnit\Framework\TestCase;
use App\Lib\Records\FieldsCollection;
use App\Lib\Records\EditableFieldsCollection;

final class EditableFieldsCollectionTest extends TestCase
{
    public function testTryGetFieldValueNachname()
    {
        $collection = $this->createEditableCollection();
        $result = $collection->tryGetFieldValue("Nachname", $value);
        $this->assertEquals("Berends", $value);
        $this->assertEquals(true, $result);
    }

    public function testEditValueAlter()
    {
        $collection = $this->createEditableCollection();
        $this->assertEquals(true, $collection->editValue("Alter", 50));
        $this->assertEquals(true, $collection->isEdited());
        $this->assertEquals(50, $collection->getFieldValue("Alter"));
    }

    public function testResetValues()
    {
        $collection = $this->createEditableCollection();
        $this->assertEquals(true, $collection->editValue("Alter", 50));
        $collection->resetValues();
        $this->assertEquals(false, $collection->isEdited());
        $this->assertEquals(49, $collection->getFieldValue("Alter"));
    }

    public function testUpdatesValues()
    {
        $collection = $this->createEditableCollection();
        $this->assertEquals(true, $collection->editValue("Alter", 50));
        $collection->updatesValues();
        $this->assertEquals(false, $collection->isEdited());
        $this->assertEquals(50, $collection->getFieldValue("Alter"));
    }

    public function testIsValueEdited()
    {
        $collection = $this->createEditableCollection();
        $this->assertEquals(true, $collection->editValue("Alter", 50));
        $this->assertEquals(true, $collection->isValueEdited("Alter"));
        $this->assertEquals(false, $collection->isValueEdited("Vorname"));
    }

    public function testIsValueEditedNoChange()
    {
        $collection = $this->createEditableCollection();
        $this->assertEquals(false, $collection->editValue("Alter", 49));
        $this->assertEquals(false, $collection->isEdited());
    }

    public function testEditValueRestore()
    {
        $collection = $this->createEditableCollection();
        $old_value = $collection->getFieldValue("Alter");
        $this->assertEquals(49, $old_value);
        $this->assertEquals(true, $collection->editValue("Alter", 50));
        $this->assertEquals(true, $collection->isValueEdited("Alter"));
        $this->assertEquals(true, $collection->editValue("Alter", $old_value));
        $this->assertEquals(false, $collection->isValueEdited("Alter"));
    }

    public function testEditUnknownFieldWohnort()
    {
        $collection = $this->createEditableCollection();
        $this->assertEquals(false, $collection->editValue("Wohnort", "Filsum"));
        $this->assertEquals(false, $collection->isValueEdited("Wohnort"));
    }

    private function createEditableCollection()
    {
        $collection = new FieldsCollection();
        $collection->initializeField("Vorname", "Bernd");
        $collection->initializeField("Nachname", "Berends");
        $collection->initializeField("Alter", 49);
        $editable = new EditableFieldsCollection($collection);
        return $editable;
    }
}
?>