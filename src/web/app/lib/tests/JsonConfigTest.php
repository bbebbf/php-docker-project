<?php
declare(strict_types=1);

namespace App\Lib\Tests;

use PHPUnit\Framework\TestCase;
use App\Lib\JsonConfig;

final class JsonConfigTest extends TestCase
{
    public function testUsername()
    {
        $config = $this->createConfig("{\"Username\": \"Hello {\$TEST_USERNAME} accessing {\$DB_NAME}.\" }");
        $this->assertEquals("Hello MyUsername accessing test_data.", $config->getValue("Username"));
    }

    public function testUnicode()
    {
        $config = $this->createConfig("{\"Username\": \"ÄÖÜ {\$TEST_USERNAME} ßaccessing {\$TEST_DBNAME} ß\" }");
        $this->assertEquals("ÄÖÜ MyUsername ßaccessing MyDbName ß", $config->getValue("Username"));
    }

    public function testUnknownVarName()
    {
        $config = $this->createConfig("{\"Username\": \"Hello {\$UnkownVarNameXYZ}!\" }");
        $this->assertEquals("Hello !", $config->getValue("Username"));
    }

    public function testUnicodeVarName()
    {
        $config = $this->createConfig("{\"Username\": \"Hello {\$USÄRNÄME}.\" }");
        $this->assertEquals("Hello .", $config->getValue("Username"));
    }

    public function testLoadFilepath()
    {
        $config = JsonConfig::load(dirname(__FILE__)."/testconf.json");
        $this->assertEquals("Are you happy?", $config->getValue("Question"));
        $this->assertEquals("Maybe", $config->getValue("Answer"));
    }

    private function createConfig($json)
    {
        $config = new JsonConfig($json);
        return $config;
    }
}
?>