<?php
declare(strict_types=1);
namespace Sypets\OsbibxParser\Style\ParseStyle\Xml;

use Sypets\OsbibxParser\Style\ParseStyle\ParseResult;

class ParseResultRawXml extends ParseResult
{
    protected array $rawValues;

    public function loadRawValues(array $values): void
    {
        $this->rawValues = $values;
        $this->parseRawValues();
    }

    protected function parseRawValues(): void
    {
        $this->parsedValues = $this->rawValues;
    }

}
