<?php
declare(strict_types=1);

namespace App\Lib;

use App\Lib\Records\Utils\FieldsMap;

class JsonConfig {

    public static function load(string $filepath) {
        $content = file_get_contents($filepath);
        if ($content === false) {
            $content = "";
        }
        else {
            // Removing UTF8-BOM
            if (strpos($content, "\xEF\xBB\xBF") === 0) {
                $content = substr($content, 3);
            }
        }
        return new JsonConfig($content);
    }

    function __construct(string $jsonstr) {
        $this->jsonstr = $jsonstr;
    }

    public function getValue(string $key): mixed {
        $this->readJson();
        return $this->values->getValue($key);
    }

    public function tryGetValue(string $key, mixed &$value): bool {
        $this->readJson();
        return $this->values->tryGetValue($fieldName, $value);
    }

    public function keyExists(string $key): bool {
        $this->readJson();
        return $this->values->keyExists($key);
    }

    private function readJson() {
        if (!is_null($this->values)) {
            return;
        }
        $this->values = new FieldsMap();
        $decoded = json_decode($this->jsonstr, true, 2);
        if (!is_array($decoded)) {
            return;
        }
        foreach ($decoded as $key => $value) {
            $this->values->setValue($key, $this->resolveValue($value));
        }
    }

    private function resolveValue(string $value): string {
        $regex = "~{\\$\w+(\/i|\/f|\/b)?\}~ui";
        $PREG_OFFSET_CAPTURE = 256;
        $fnd = preg_match_all($regex, $value, $matches_arr, $PREG_OFFSET_CAPTURE);
        if ($fnd == 0 || !is_array($matches_arr)) {
            return $value;
        }
        $result = "";
        $previous_pattern_start_idx = strlen($value) + 1;
        foreach (array_reverse($matches_arr[0]) as $item) {
            $pattern_start_byte_idx = $item[1];
            $pattern_byte_length = strlen($item[0]);

            $substr_start_index = $pattern_start_byte_idx + $pattern_byte_length;
            $substr_length = $previous_pattern_start_idx - $substr_start_index;
            $result = substr($value, $substr_start_index, $substr_length).$result;

            $env_varname = substr($item[0], 2, $pattern_byte_length - 3);
            if (array_key_exists($env_varname, $_ENV)) {
                $result = $_ENV[$env_varname].$result;
            }

            $previous_pattern_start_idx = $pattern_start_byte_idx;
        }
        $result = substr($value, 0, $previous_pattern_start_idx).$result;
        return $result;
    }

    private $jsonstr;
    private $values = null;
}
?>