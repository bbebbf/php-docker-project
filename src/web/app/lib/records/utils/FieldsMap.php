<?php
declare(strict_types=1);

namespace App\Lib\Records\Utils;

class FieldsMap {

    public function clear() {
        $this->entries = array();
    }

    public function isEmpty(): bool {
        return count($this->entries) == 0;
    }

    public function keyExists(string $key): bool {
        return array_key_exists($this->getNormalizedKey($key), $this->entries);
    }

    public function getKeys(): array {
        $response = array();
        foreach ($this->entries as $key => $value) {
            $response[] = $key;
        }
        return $response;
    }

    public function getValue(string $key): mixed {
        if ($this->tryGetValue($key, $value)) {
            return $value;
        }
        return null;
    }

    public function tryGetValue(string $key, mixed &$value): bool {
        $normalizedKey = $this->getNormalizedKey($key);
        if (array_key_exists($normalizedKey, $this->entries)) {
            $value = $this->entries[$normalizedKey];
            return true;
        }
        $value = null;
        return false;
    }

    public function setValue(string $key, $value) {
        $this->entries[$this->getNormalizedKey($key)] = $value;
    }

    public function deleteKey(string $key): bool {
        $normalizedKey = $this->getNormalizedKey($key);
        if (array_key_exists($normalizedKey, $this->entries)) {
            unset($this->entries[$normalizedKey]);
            return true;
        }
        return false;
    }

    private function getNormalizedKey(string $key): string {
        return strtolower($key);
    }

    private $entries = array();
}
?>