<?php
declare(strict_types=1);
namespace Sypets\OsbibxParser\Style\ParseStyle;

interface ParseResultInterface
{
    public function getInfoArray(): array;
    public function getCitationArray(): array;
    public function getFootnoteArray(): array;
    public function getCommonArray(): array;
    public function getTypesArray(): array;

}
