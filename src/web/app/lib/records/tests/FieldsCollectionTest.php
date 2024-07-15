<?php
declare(strict_types=1);

namespace App\Lib\Records\Tests;

use PHPUnit\Framework\TestCase;
use App\Lib\Records\FieldsCollection;

final class FieldsCollectionTest extends TestCase
{
    public function testGetFieldValueVorname()
    {
        $collection = $this->createCollection();
        $this->assertEquals("Bernd", $collection->getFieldValue("Vorname"));
        $this->assertEquals("Bernd", $collection->getFieldValue("vorname"));
    }

    public function testGetFieldValueAlter()
    {
        $collection = $this->createCollection();
        $this->assertEquals(49, $collection->getFieldValue("Alter"));
        $this->assertEquals(49, $collection->getFieldValue("alter"));
    }

    public function testGetFieldValueWohnort()
    {
        $collection = $this->createCollection();
        $this->assertEquals(null, $collection->getFieldValue("Wohnort"));
    }

    public function testTryGetFieldValueNachname()
    {
        $collection = $this->createCollection();
        $result = $collection->tryGetFieldValue("Nachname", $value);
        $this->assertEquals("Berends", $value);
        $this->assertEquals(true, $result);
    }

    public function testTryGetFieldValueAlter()
    {
        $collection = $this->createCollection();
        $result = $collection->tryGetFieldValue("Alter", $value);
        $this->assertEquals(49, $value);
        $this->assertEquals(true, $result);
    }

    public function testTryGetFieldValueWohnort()
    {
        $collection = $this->createCollection();
        $result = $collection->tryGetFieldValue("Wohnort", $value);
        $this->assertEquals(null, $value);
        $this->assertEquals(false, $result);
    }

    public function testFieldExists()
    {
        $collection = $this->createCollection();
        $this->assertEquals(true, $collection->fieldExists("Vorname"));
    }

    public function testFieldExistsFALSE()
    {
        $collection = $this->createCollection();
        $this->assertEquals(false, $collection->fieldExists("Hobbies"));
    }

    private function createCollection()
    {
        $collection = new FieldsCollection();
        $collection->initializeField("Vorname", "Bernd");
        $collection->initializeField("Nachname", "Berends");
        $collection->initializeField("Alter", 49);
        return $collection;
    }
}
?>