<?php
declare(strict_types=1);

namespace SelectPages;

class SqlParamsAccessor {

    private array $parsedParamsIndexes = [];
    private array $parsedParamsTypes = [];
    private array $parsedParamsValues = [];

    public function __construct() {}

    public function getParamCount(): int {
        return count($this->parsedParamsIndexes);
    }

    public function getParamNameByIndex(int $paramIndex): string {
        if (array_key_exists($paramIndex, $this->parsedParamsIndexes) === true) {
            return $this->parsedParamsIndexes[$paramIndex];
        }
        return '';
    }

    public function getParamTypeByName(string $paramName): int {
        if (array_key_exists($paramName, $this->parsedParamsTypes) === true) {
            return $this->parsedParamsTypes[$paramName];
        }
        return -1;
    }

    public function setParamTypeByName(string $paramName, int $paramType): void {
        $this->parsedParamsTypes[$paramName] = $paramType;
    }

    public function getParamValueByName(string $paramName): mixed {
        if (array_key_exists($paramName, $this->parsedParamsValues) === true) {
            return $this->parsedParamsValues[$paramName];
        }
        return null;
    }

    public function setParamValueByName(string $paramName, mixed $paramValue): void {
        $this->parsedParamsValues[$paramName] = $paramValue;
    }

    public function parse(string $sqlStmtString): string {
        $this->parsedParamsIndexes = [];
        $paramIndex = 0;
        $parts = explode(':', $sqlStmtString);
        $newParts = [];

        for ($i = 0; $i < count($parts); $i++) {

            $newPart = $parts[$i];

            $WORD_SEPARATORS = "\t\r\n ().,;=+-*/%<>!&|^";
            $firstWord = strtok($newPart, $WORD_SEPARATORS);
            if ($firstWord === false) {
                $firstWord = '';
            }
            if ($i > 0 && mb_strlen($firstWord) > 0) {
                $paramIndex++;
                $this->parsedParamsIndexes[$paramIndex] = $firstWord;
                $newPart = substr($newPart, mb_strlen($firstWord));
            }
            $newParts[] = $newPart;
        }
        return implode('?', $newParts);
    }
}