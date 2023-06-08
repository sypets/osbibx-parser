<?php

namespace Sypets\OsbibxParser\Style\ParseStyle;

/**
 * XML styles converted into respective array for footnote, bibliography/common etc.
 * The styles can be converted into a Styles class.
 */
class ParseResult implements ParseResultInterface
{
    protected array $info = [];
    protected array $bibliographyCommon = [];
    protected array $types = [];
    protected array $footnoteCommon = [];
    protected array $footnoteTypes = [];
    protected array $citation = [];

    public function __construct(array $info, array $bibliographyCommon, array $types, array $footnoteCommon,
        array $footnoteTypes, array $citation)
    {
        $this->info = $info;
        $this->bibliographyCommon = $bibliographyCommon;
        $this->types = $types;
        $this->footnoteCommon = $footnoteCommon;
        $this->footnoteTypes = $footnoteTypes;
        $this->citation = $citation;
    }

    public function getInfoArray(): array
    {
        return $this->info;
    }

    public function getBibliographyCommonArray(): array
    {
        return $this->bibliographyCommon;
    }

    /**
     * @todo possibly rename to getBibliogrphyTypesArray
     */
    public function getTypesArray(): array
    {
        return $this->types;
    }

    public function getFootnoteCommonArray(): array
    {
        return $this->footnoteCommon;
    }

    public function getFootnoteTypesArray(): array
    {
        return $this->footnoteCommon;
    }

    public function getCitationArray(): array
    {
        return $this->citation;
    }
}
