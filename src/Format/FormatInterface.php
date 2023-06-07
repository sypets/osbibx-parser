<?php

namespace Sypets\OsbibxParser\Format;

use Sypets\OsbibxParser\Utf8;

interface FormatInterface
{
    public function getPatterns(): string;
    public function resetPatterns(): void;
    public function getPatternHighlight(): string;
    public function setPatternHighlight(string $patternHighlight): void;
    public function getUtf8(): ?Utf8;
    public function setOutput(string $output): void;
    public function getOutput(): string;
    /**
     * @deprecated
     * @todo remove Wikindx specific functionality
     */
    public function setWikindx(bool $wikindx): void;
    public function getWikindx(): bool;
}
