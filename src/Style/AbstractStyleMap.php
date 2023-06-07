<?php

declare(strict_types=1);
namespace Sypets\OsbibxParser\Style;

abstract class AbstractStyleMap implements StyleMapInterface
{
    protected const CREATOR_TYPE_PRIMARY = 'primary';
    protected const CREATOR_TYPE_OTHER = 'other';

    /**
     * Use this type for unknown type
     *
     * @var string
     */
    const DEFAULT_TYPE = 'genericMisc';

    protected const DEFAULT_CITATION_ENDNOTE_IN_TEXT = [
        'id' => 'id',
        'pages' => 'pages',
    ];

    protected const DEFAULT_CITATION_ENDNOTE = [
        'citation' => 'citation',
        'creator' => 'creator',
        'title'	=> 'title',
        'year' => 'year',
        'pages' => 'pages',
    ];

    protected array $unpublished = [];
    protected array $music_score = [];
    protected array $music_track = [];
    protected array $music_album = [];
    protected array $proceedings = [];
    protected array $personal = [];
    protected array $patent = [];
    protected array $statute = [];
    protected array $chart = [];
    protected array $map = [];
    protected array $manuscript = [];
    protected array $database = [];
    protected array $hearing = [];
    protected array $report = [];
    protected array $government_report = [];
    protected array $miscellaneous = [];
    protected array $conference_paper = [];
    protected array $classical = [];
    protected array $bill = [];
    protected array $legal_ruling = [];
    protected array $case = [];
    protected array $audiovisual = [];
    protected array $artwork = [];
    protected array $software = [];
    protected array $broadcast = [];
    protected array $film = [];
    protected array $web_article = [];
    protected array $thesis = [];
    protected array $proceedings_article = [];
    protected array $magazine_article = [];
    protected array $newspaper_article = [];
    protected array $journal_article = [];
    protected array $book_article = [];
    protected array $book = [];
    protected array $genericMisc = [];
    protected array $genericArticle = [];
    protected array $genericBook = [];


    protected array $basic = [];

    // -----------------
    // common properties
    // -----------------

    /**
     * Contains common settings (from style/bibliography/common) such as titleCapitalization
     * Some settings are transformed to their own variables to be able to retrieve them more easily and use correct
     * types.
     */
    protected array $common = [];
    /** Should title be capitalized (start with captital letter)? */
    protected bool $titleCapitalization = false;

    protected bool $editorSwitch = false;
    protected string $editorSwitchIfYes = '';
    protected bool $dateMonthNoDay = false;
    protected string $dateMonthNoDayString = '';
    protected int $runningTimeFormat = 0;

    /**
     * day format
     * 1: e.g. 10.
     * 2: e.g. 10th
     * @todo Use more expressive format types than numeric values
     */
    protected int $dayFormat = 0;
    /**
     * Month format:
     * 0: Full month name
     * 1: user defined
     * @todo Use more expressive format types than numeric values
     */
    protected int $monthFormat = 0;

    protected string $dateRangeDelimit1 = '';
    protected string $dateRangeDelimit2 = '';
    protected bool $dateRangeSameMonth = false;
    /**
     * 1: e.g. 10.
     * 2: e.g. 10th
     * @todo Use more expressive format types than integer
     */
    protected int $editionFormat = 0;

    // styles for "primary" and "other"
    // @todo the format should be changed in the XML so that all primary elements are encapsulated in a primary element
    // @todo make this more generic so we do not need 2 variables for each?
    //   also we have all for footnote and bibliography
    protected bool $primaryCreatorListLimit = false;
    protected bool $otherCreatorListLimit = false;

    // *CreatorListMore
    protected bool $primaryCreatorListMore = false;
    protected bool $otherCreatorListMore = false;

    // *CreatorListAbbreviation
    protected bool $primaryCreatorListAbbreviation = false;
    protected bool $otherCreatorListAbbreviation = false;

    protected bool $primaryCreatorInitials = false;
    protected bool $otherCreatorInitials = false;

    protected bool $primaryCreatorFirstName = true;
    protected bool $otherCreatorFirstName = true;

    // *TwoCreatorsSep
    protected bool $primaryTwoCreatorsSep = true;
    protected bool $otherTwoCreatorsSep = true;

    // *CreatorSepFirstBetween
    protected bool $primaryCreatorSepFirstBetween = true;
    protected bool $otherCreatorSepFirstBetween = true;

    // *CreatorSepNextBetween
    protected bool $primaryCreatorSepNextBetween = true;
    protected bool $otherCreatorSepNextBetween = true;

    protected bool $primaryCreatorSepNextLast = true;
    protected bool $otherCreatorSepNextLast = true;

    // *CreatorUppercase
    protected bool $primaryCreatorUppercase = true;
    protected bool $otherCreatorUppercase = true;

    // *CreatorListAbbreviationItalic
    protected bool $primaryCreatorListAbbreviationItalic = true;
    protected bool $otherCreatorListAbbreviationItalic = true;

    protected bool $primaryCreatorFirstStyle = false;
    protected bool $otherCreatorFirstStyle = true;

    protected bool $primaryCreatorOtherStyle = false;
    protected bool $otherCreatorOtherStyle = true;






    // ------------------------
    // END of common properties
    // ------------------------

    // -------------------
    // citation properties
    // -------------------

    /**
     * What fields are available to the in-text citation template? This array should NOT be changed.
     */
    protected array $citation = [];

    /**
     * What fields are available to the in-text citation template for endnote-style citations? This array should NOT be changed.
     */
    protected array $citationEndnoteInText = [];

    /**
     * What fields are available to the endnote citation template for endnote-style citations? This array should NOT be changed.
     */
    protected array $citationEndnote = [];

    protected array $types = [];

    public function __construct()
    {
        // loadMapBasic(), implemented in this class
        $this->loadMapBasic();

        // call loadMap(), implemented in inheriting class
        $this->loadMap();
    }

    protected function loadMapBasic()
    {
        /**
         * What fields are available to the in-text citation template? This array should NOT be changed.
         */
        $this->citation = [
            'creator' => 'creator',
            'title'	=> 'title',
            'year' => 'year',
            'pages' => 'pages',
        ];

        /**
         * What fields are available to the in-text citation template for endnote-style citations? This array should NOT be changed.
         */
        $this->citationEndnoteInText = [
            'id' => 'id',
            'pages' => 'pages',
        ];
        /**
         * What fields are available to the endnote citation template for endnote-style citations? This array should NOT be changed.
         */
        $this->citationEndnote = [
            'citation' => 'citation',
            'creator' => 'creator',
            'title' => 'title',
            'year' => 'year',
            'pages' => 'pages',
        ];

        /**
         * Basic array of elements common to all types - change the key to map the database field that stores that value.
         */
        $this->basic = [
            'title' => 'title',
            'year1' => 'publicationYear',
        ];

    }

    /**
     * @param string $type
     * @return false|int|string
     */
    public function mapType(string $type)
    {
        $mappedType = array_search($type, $this->types);

        return $mappedType;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getCitation(): array
    {
        return $this->citation;
    }

    public function getCitationEndNote(): array
    {
        return $this->citationEndnote;
    }

    public function getCitationEndNoteInText(): array
    {
        return $this->citationEndnoteInText;
    }

    /**
     * @param string $propertyName
     * @return string|array
     */
    public function getDynamicProperty(string $propertyName)
    {
        return $this->$propertyName;
    }

    public function getDynamicPropertyArrayElement(string $propertyName, string $arrayElement): string
    {
        return $this->$propertyName[$arrayElement];
    }

    public function setCommonValue(string $field, string $value): void
    {
        $this->common[$field] = $value;
    }

    public function isTitleCapitalization(): bool
    {
        return $this->titleCapitalization;
    }
    public function setTitleCapitalization(bool $titleCapitalization): void
    {
        $this->titleCapitalization = $titleCapitalization;
    }

    /**
     * @return bool
     */
    public function isEditorSwitch(): bool
    {
        return $this->editorSwitch;
    }

    /**
     * @param bool $editorSwitch
     */
    public function setEditorSwitch(bool $editorSwitch): void
    {
        $this->editorSwitch = $editorSwitch;
    }

    /**
     * @return string
     */
    public function getEditorSwitchIfYes(): string
    {
        return $this->editorSwitchIfYes;
    }

    /**
     * @param string $editorSwitchIfYes
     */
    public function setEditorSwitchIfYes(string $editorSwitchIfYes): void
    {
        $this->editorSwitchIfYes = $editorSwitchIfYes;
    }

    /**
     * @return bool
     */
    public function isDateMonthNoDay(): bool
    {
        return $this->dateMonthNoDay;
    }

    /**
     * @param bool $dateMonthNoDay
     */
    public function setDateMonthNoDay(bool $dateMonthNoDay): void
    {
        $this->dateMonthNoDay = $dateMonthNoDay;
    }

    /**
     * @return string
     */
    public function getDateMonthNoDayString(): string
    {
        return $this->dateMonthNoDayString;
    }

    /**
     * @param string $dateMonthNoDayString
     */
    public function setDateMonthNoDayString(string $dateMonthNoDayString): void
    {
        $this->dateMonthNoDayString = $dateMonthNoDayString;
    }

    public function hasDateMonthNoDayString(): bool
    {
        return $this->dateMonthNoDayString !== '';
    }

    /**
     * @return int
     */
    public function getRunningTimeFormat(): int
    {
        return $this->runningTimeFormat;
    }

    /**
     * @param int $runningTimeFormat
     */
    public function setRunningTimeFormat(int $runningTimeFormat): void
    {
        $this->runningTimeFormat = $runningTimeFormat;
    }

    /**
     * @return int
     */
    public function getDayFormat(): int
    {
        return $this->dayFormat;
    }

    /**
     * @param int $dayFormat
     */
    public function setDayFormat(int $dayFormat): void
    {
        $this->dayFormat = $dayFormat;
    }

    /**
     * @return int
     */
    public function getMonthFormat(): int
    {
        return $this->monthFormat;
    }

    /**
     * @param int $monthFormat
     */
    public function setMonthFormat(int $monthFormat): void
    {
        $this->monthFormat = $monthFormat;
    }

    /**
     * @return string
     */
    public function getDateRangeDelimit1(): string
    {
        return $this->dateRangeDelimit1;
    }

    /**
     * @param string $dateRangeDelimit1
     */
    public function setDateRangeDelimit1(string $dateRangeDelimit1): void
    {
        $this->dateRangeDelimit1 = $dateRangeDelimit1;
    }

    /**
     * @return string
     */
    public function getDateRangeDelimit2(): string
    {
        return $this->dateRangeDelimit2;
    }

    /**
     * @param string $dateRangeDelimit2
     */
    public function setDateRangeDelimit2(string $dateRangeDelimit2): void
    {
        $this->dateRangeDelimit2 = $dateRangeDelimit2;
    }

    /**
     * @return bool
     */
    public function isDateRangeSameMonth(): bool
    {
        return $this->dateRangeSameMonth;
    }

    /**
     * @param bool $dateRangeSameMonth
     */
    public function setDateRangeSameMonth(bool $dateRangeSameMonth): void
    {
        $this->dateRangeSameMonth = $dateRangeSameMonth;
    }

    /**
     * @return int
     */
    public function getEditionFormat(): int
    {
        return $this->editionFormat;
    }

    /**
     * @param int $editionFormat
     */
    public function setEditionFormat(int $editionFormat): void
    {
        $this->editionFormat = $editionFormat;
    }

    public function isCreatorInitials(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorInitials();
        }
        return $this->isOtherCreatorInitials();
    }

    public function isPrimaryCreatorInitials(): bool
    {
        return $this->primaryCreatorInitials;
    }

    /**
     * @param bool $primaryCreatorInitials
     */
    public function setPrimaryCreatorInitials(bool $primaryCreatorInitials): void
    {
        $this->primaryCreatorInitials = $primaryCreatorInitials;
    }

    public function isCreatorFirstStyle(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorFirstStyle();
        }
        return $this->isOtherCreatorFirstStyle();
    }

    public function isPrimaryCreatorFirstStyle(): bool
    {
        return $this->primaryCreatorFirstStyle;
    }

    /**
     * @param bool $primaryCreatorFirstStyle
     */
    public function setPrimaryCreatorFirstStyle(bool $primaryCreatorFirstStyle): void
    {
        $this->primaryCreatorFirstStyle = $primaryCreatorFirstStyle;
    }

    public function isCreatorOtherStyle(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorOtherStyle();
        }
        return $this->isOtherCreatorOtherStyle();
    }

    public function isPrimaryCreatorOtherStyle(): bool
    {
        return $this->primaryCreatorOtherStyle;
    }

    /**
     * @param bool $primaryCreatorOtherStyle
     */
    public function setPrimaryCreatorOtherStyle(bool $primaryCreatorOtherStyle): void
    {
        $this->primaryCreatorOtherStyle = $primaryCreatorOtherStyle;
    }

    public function isCreatorFirstName(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorFirstName();
        }
        return $this->isOtherCreatorFirstName();
    }

    public function isPrimaryCreatorFirstName(): bool
    {
        return $this->primaryCreatorFirstName;
    }

    /**
     * @param bool $primaryCreatorFirstName
     */
    public function setPrimaryCreatorFirstName(bool $primaryCreatorFirstName): void
    {
        $this->primaryCreatorFirstName = $primaryCreatorFirstName;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorFirstStyle(): bool
    {
        return $this->otherCreatorFirstStyle;
    }

    /**
     * @param bool $otherCreatorFirstStyle
     */
    public function setOtherCreatorFirstStyle(bool $otherCreatorFirstStyle): void
    {
        $this->otherCreatorFirstStyle = $otherCreatorFirstStyle;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorOtherStyle(): bool
    {
        return $this->otherCreatorOtherStyle;
    }

    /**
     * @param bool $otherCreatorOtherStyle
     */
    public function setOtherCreatorOtherStyle(bool $otherCreatorOtherStyle): void
    {
        $this->otherCreatorOtherStyle = $otherCreatorOtherStyle;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorInitials(): bool
    {
        return $this->otherCreatorInitials;
    }

    /**
     * @param bool $otherCreatorInitials
     */
    public function setOtherCreatorInitials(bool $otherCreatorInitials): void
    {
        $this->otherCreatorInitials = $otherCreatorInitials;
    }

    /**
     * @param string $creator "primary" or "other"
     * @return bool
     */
    public function isCreatorListLimit(string $creator): bool
    {
        if ($creator === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorListLimit();
        }
        return $this->isOtherCreatorListLimit();
    }

    /**
     * @return bool
     */
    public function isPrimaryCreatorListLimit(): bool
    {
        return $this->primaryCreatorListLimit;
    }

    /**
     * @param bool $primaryCreatorListLimit
     */
    public function setPrimaryCreatorListLimit(bool $primaryCreatorListLimit): void
    {
        $this->primaryCreatorListLimit = $primaryCreatorListLimit;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorListLimit(): bool
    {
        return $this->otherCreatorListLimit;
    }

    /**
     * @param bool $otherCreatorListLimit
     */
    public function setOtherCreatorListLimit(bool $otherCreatorListLimit): void
    {
        $this->otherCreatorListLimit = $otherCreatorListLimit;
    }

    public function isCreatorListMore(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorListMore();
        }
        return $this->isOtherCreatorListMore();
    }

    public function isPrimaryCreatorListMore(): bool
    {
        return $this->primaryCreatorListMore;
    }

    /**
     * @param bool $primaryCreatorListMore
     */
    public function setPrimaryCreatorListMore(bool $primaryCreatorListMore): void
    {
        $this->primaryCreatorListMore = $primaryCreatorListMore;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorListMore(): bool
    {
        return $this->otherCreatorListMore;
    }

    /**
     * @param bool $otherCreatorListMore
     */
    public function setOtherCreatorListMore(bool $otherCreatorListMore): void
    {
        $this->otherCreatorListMore = $otherCreatorListMore;
    }

    public function isCreatorListAbbreviation(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorListAbbreviation();
        }
        return $this->isOtherCreatorListAbbreviation();
    }

    public function isPrimaryCreatorListAbbreviation(): bool
    {
        return $this->primaryCreatorListAbbreviation;
    }

    /**
     * @param bool $primaryCreatorListAbbreviation
     */
    public function setPrimaryCreatorListAbbreviation(bool $primaryCreatorListAbbreviation): void
    {
        $this->primaryCreatorListAbbreviation = $primaryCreatorListAbbreviation;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorListAbbreviation(): bool
    {
        return $this->otherCreatorListAbbreviation;
    }

    /**
     * @param bool $otherCreatorListAbbreviation
     */
    public function setOtherCreatorListAbbreviation(bool $otherCreatorListAbbreviation): void
    {
        $this->otherCreatorListAbbreviation = $otherCreatorListAbbreviation;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorFirstName(): bool
    {
        return $this->otherCreatorFirstName;
    }

    /**
     * @param bool $otherCreatorFirstName
     */
    public function setOtherCreatorFirstName(bool $otherCreatorFirstName): void
    {
        $this->otherCreatorFirstName = $otherCreatorFirstName;
    }

    public function isTwoCreatorsSep(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryTwoCreatorsSep();
        }
        return $this->isOtherTwoCreatorsSep();
    }

    public function isPrimaryTwoCreatorsSep(): bool
    {
        return $this->primaryTwoCreatorsSep;
    }

    /**
     * @param bool $primaryTwoCreatorsSep
     */
    public function setPrimaryTwoCreatorsSep(bool $primaryTwoCreatorsSep): void
    {
        $this->primaryTwoCreatorsSep = $primaryTwoCreatorsSep;
    }

    /**
     * @return bool
     */
    public function isOtherTwoCreatorsSep(): bool
    {
        return $this->otherTwoCreatorsSep;
    }

    /**
     * @param bool $otherTwoCreatorsSep
     */
    public function setOtherTwoCreatorsSep(bool $otherTwoCreatorsSep): void
    {
        $this->otherTwoCreatorsSep = $otherTwoCreatorsSep;
    }

    public function isCreatorSepFirstBetween(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorSepFirstBetween();
        }
        return $this->isOtherCreatorSepFirstBetween();
    }

    public function isPrimaryCreatorSepFirstBetween(): bool
    {
        return $this->primaryCreatorSepFirstBetween;
    }

    /**
     * @param bool $primaryCreatorSepFirstBetween
     */
    public function setPrimaryCreatorSepFirstBetween(bool $primaryCreatorSepFirstBetween): void
    {
        $this->primaryCreatorSepFirstBetween = $primaryCreatorSepFirstBetween;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorSepFirstBetween(): bool
    {
        return $this->otherCreatorSepFirstBetween;
    }

    /**
     * @param bool $otherCreatorSepFirstBetween
     */
    public function setOtherCreatorSepFirstBetween(bool $otherCreatorSepFirstBetween): void
    {
        $this->otherCreatorSepFirstBetween = $otherCreatorSepFirstBetween;
    }

    public function isCreatorSepNextBetween(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorSepNextBetween();
        }
        return $this->isOtherCreatorSepNextBetween();
    }

    public function isPrimaryCreatorSepNextBetween(): bool
    {
        return $this->primaryCreatorSepNextBetween;
    }

    /**
     * @param bool $primaryCreatorSepNextBetween
     */
    public function setPrimaryCreatorSepNextBetween(bool $primaryCreatorSepNextBetween): void
    {
        $this->primaryCreatorSepNextBetween = $primaryCreatorSepNextBetween;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorSepNextBetween(): bool
    {
        return $this->otherCreatorSepNextBetween;
    }

    /**
     * @param bool $otherCreatorSepNextBetween
     */
    public function setOtherCreatorSepNextBetween(bool $otherCreatorSepNextBetween): void
    {
        $this->otherCreatorSepNextBetween = $otherCreatorSepNextBetween;
    }

    public function isCreatorUppercase(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorUppercase();
        }
        return $this->isOtherCreatorUppercase();
    }

    public function isPrimaryCreatorUppercase(): bool
    {
        return $this->primaryCreatorUppercase;
    }

    /**
     * @param bool $primaryCreatorUppercase
     */
    public function setPrimaryCreatorUppercase(bool $primaryCreatorUppercase): void
    {
        $this->primaryCreatorUppercase = $primaryCreatorUppercase;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorUppercase(): bool
    {
        return $this->otherCreatorUppercase;
    }

    /**
     * @param bool $otherCreatorUppercase
     */
    public function setOtherCreatorUppercase(bool $otherCreatorUppercase): void
    {
        $this->otherCreatorUppercase = $otherCreatorUppercase;
    }

    public function isCreatorListAbbreviationItalic(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorListAbbreviation();
        }
        return $this->isOtherCreatorListAbbreviation();
    }

    public function isPrimaryCreatorListAbbreviationItalic(): bool
    {
        return $this->primaryCreatorListAbbreviationItalic;
    }

    /**
     * @param bool $primaryCreatorListAbbreviationItalic
     */
    public function setPrimaryCreatorListAbbreviationItalic(bool $primaryCreatorListAbbreviationItalic): void
    {
        $this->primaryCreatorListAbbreviationItalic = $primaryCreatorListAbbreviationItalic;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorListAbbreviationItalic(): bool
    {
        return $this->otherCreatorListAbbreviationItalic;
    }

    /**
     * @param bool $otherCreatorListAbbreviationItalic
     */
    public function setOtherCreatorListAbbreviationItalic(bool $otherCreatorListAbbreviationItalic): void
    {
        $this->otherCreatorListAbbreviationItalic = $otherCreatorListAbbreviationItalic;
    }

    public function isCreatorSepNextLast(string $creatorType): bool
    {
        if ($creatorType === self::CREATOR_TYPE_PRIMARY) {
            return $this->isPrimaryCreatorSepNextLast();
        }
        return $this->isOtherCreatorSepNextLast();
    }

    public function isPrimaryCreatorSepNextLast(): bool
    {
        return $this->primaryCreatorSepNextLast;
    }

    /**
     * @param bool $primaryCreatorSepNextLast
     */
    public function setPrimaryCreatorSepNextLast(bool $primaryCreatorSepNextLast): void
    {
        $this->primaryCreatorSepNextLast = $primaryCreatorSepNextLast;
    }

    /**
     * @return bool
     */
    public function isOtherCreatorSepNextLast(): bool
    {
        return $this->otherCreatorSepNextLast;
    }

    /**
     * @param bool $otherCreatorSepNextLast
     */
    public function setOtherCreatorSepNextLast(bool $otherCreatorSepNextLast): void
    {
        $this->otherCreatorSepNextLast = $otherCreatorSepNextLast;
    }
}
