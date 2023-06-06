<?php

declare(strict_types=1);
namespace Sypets\OsbibxParser\Format;

use Sypets\OsbibxParser\Utf8;

abstract class AbstractFormat implements FormatInterface
{
    /** @todo make protected */
    public string $output = '';

    protected ?Utf8 $utf8 = null;

    /** @todo specify datatypes */
    protected array $style = [];
    protected array $item = [];
    protected bool $wikindx = false;
    protected string $patternHighlight = '';

    /**
     * @todo is effectively not really used, remove this property and related methods
     */
    protected string $patterns = '';

    /**
     * Is not called getStyle() because this function already exists. Has been deprecated and renamed
     * to loadStyles(). We can rename this function later.
     *
     * @return array
     */
    public function getStyleArray(): array
    {
        return $this->style;
    }

    public function hasStyle(string $id): bool
    {
        return (bool)($this->style[$id] ?? false);
    }

    public function getPatterns(): string
    {
        return $this->patterns;
    }

    public function resetPatterns(): void
    {
        $this->patterns = '';
    }

    public function getPatternHighlight(): string
    {
        return $this->patternHighlight;
    }

    public function setPatternHighlight(string $patternHighlight): void
    {
        $this->patternHighlight = $patternHighlight;
    }

    public function getUtf8(): ?Utf8
    {
        return $this->utf8;
    }

    public function setOutput(string $output): void
    {
        $this->output = $output;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function setWikindx(bool $wikindx): void
    {
        $this->wikindx = $wikindx;
    }

    public function getWikindx(): bool
    {
        return $this->wikindx;
    }
}
