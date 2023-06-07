<?php

namespace Sypets\OsbibxParser\Style\ParseStyle;

class ParseResult implements ParseResultInterface
{
    /**
     * @var array{'info': array<mixed>, 'footnote': array<mixed>, 'common': array<mixed>, 'types': array<mixed>}
     */
    protected array $parsedValues = [];

    public function getInfoArray(): array
    {
        return $this->parsedValues['info'] ?? [];
    }
    public function getCitationArray(): array
    {
        return $this->parsedValues['citation']?? [];
    }
    public function getFootnoteArray(): array
    {
        return $this->parsedValues['footnote']?? [];
    }
    public function getCommonArray(): array
    {
        return $this->parsedValues['common']?? [];
    }

    public function getTypesArray(): array
    {
        return $this->parsedValues['types']?? [];
    }
}
