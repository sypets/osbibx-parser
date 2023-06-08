<?php
declare(strict_types=1);
namespace Sypets\OsbibxParser\Style\ParseStyle;

interface ParseResultInterface
{
    public function getInfoArray(): array;
    public function getCitationArray(): array;
    public function getFootnoteCommonArray(): array;
    public function getFootnoteTypesArray(): array;
    public function getBibliographyCommonArray(): array;
    public function getTypesArray(): array;
}
