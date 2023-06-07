<?php

declare(strict_types=1);
namespace Sypets\OsbibxParser\Create;

use Sypets\OsbibxParser\Format\Bibformat;
use Sypets\OsbibxParser\Style\Stylemap;

/********************************
OSBib:
A collection of PHP classes to create and manage bibliographic formatting for OS bibliography software
using the OSBib standard.

Released through http://bibliophile.sourceforge.net under the GPL licence.
Do whatever you like with this -- some credit to the author(s) would be appreciated.

If you make improvements, please consider contacting the administrators at bibliophile.sourceforge.net
so that your improvements can be added to the release package.

Adapted from WIKINDX: http://wikindx.sourceforge.net

Mark Grimshaw 2005
http://bibliophile.sourceforge.net
********************************/

/**
* TEMPLATE PREVIEW class.
*
* Preview bibliographic style templates.
*
* $Header: /cvsroot/bibliophile/OSBib/create/Previewstyle.php,v 1.4 2005/11/14 06:38:15 sirfragalot Exp $
*/
class Previewstyle
{
    protected bool $footnotePages = false;
    protected bool $pages = false;
    protected array $vars = [];
    protected array $row = [];
    protected array $creator1 = [];
    protected array $creator2 = [];
    protected array $creator3 = [];
    protected array $creator4 = [];
    protected array $creator5 = [];
    protected ?Messages $messages = null;
    protected ?Errors $errors = null;
    protected ?Bibformat $bibformat = null;
    protected ?Stylemap $map = null;
    protected ?Misc $misc = null;
    protected ?Adminstyle $adminStyle = null;

    public function __construct($vars)
    {
        $this->vars = $vars;
        $this->misc = new Misc();
        $this->messages = new Messages();
        $this->errors = new Errors();
        $this->bibformat = new Bibformat('', false, true, false);
        $this->footnotePages = false;
    }

    /**
    * display
    *
    * @author Mark Grimshaw
    */
    public function display()
    {
        $this->adminStyle = new Adminstyle($this->vars);
        $map = new Stylemap();
        $templateNameArray = mb_split('_', stripslashes($this->vars['templateName']), 2);
        $type = $templateNameArray[1];
        $templateString = preg_replace(
            "/%u(\d+)/",
            '&#x$1;',
            stripslashes(urldecode($this->vars['templateString']))
        );
        if (!$templateString) {
            return $this->errors->text('inputError', 'missing');
        }
        $templateArray = $this->adminStyle->parseStringToArray($type, $templateString, $map);
        if (!$templateArray) {
            return $this->errors->text('inputError', 'invalid');
        }

        /**
         * @todo get rid of unserialize
         * "Warning: Do not pass untrusted user input to unserialize() regardless of the options value of
         * allowed_classes. Unserialization can result in code being loaded and executed due to object instantiation
         * and autoloading, and a malicious user may be able to exploit this. Use a safe, standard data interchange
         * format such as JSON (via json_decode() and json_encode()) if you need to pass serialized data to the user."
         * https://www.php.net/manual/en/function.unserialize.php
         */
        $style = unserialize(stripslashes(urldecode($this->vars['style'])));
        foreach ($style as $key => $value) {
            // Convert javascript unicode e.g. %u2014 to HTML entities
            $value = preg_replace("/%u(\d+)/", '&#x$1;', str_replace('__WIKINDX__SPACE__', ' ', $value));
            //$this->bibformat->style[str_replace('style_', '', $key)] = $value;
            $this->bibformat->setStyleEntry(str_replace('style_', '', $key), $value);
        }
        $this->bibformat->loadArrays();
        if (array_key_exists('independent', $templateArray)) {
            $temp = $templateArray['independent'];
            $independent = [];
            foreach ($temp as $key => $value) {
                $split = mb_split('_', $key);
                $independent[$split[1]] = $value;
            }
            $templateArray['independent'] = $independent;
        }
        $this->bibformat->$type = $templateArray;

        /**
         * @todo get rid of unserialize
         * "Warning: Do not pass untrusted user input to unserialize() regardless of the options value of
         * allowed_classes. Unserialization can result in code being loaded and executed due to object instantiation
         * and autoloading, and a malicious user may be able to exploit this. Use a safe, standard data interchange
         * format such as JSON (via json_decode() and json_encode()) if you need to pass serialized data to the user."
         * https://www.php.net/manual/en/function.unserialize.php
         */
        $rewriteCreator = unserialize(stripslashes(urldecode($this->vars['rewriteCreator'])));
        foreach ($rewriteCreator as $key => $value) {
            // Convert javascript unicode e.g. %u2014 to HTML entities
            $value = preg_replace("/%u(\d+)/", '&#x$1;', str_replace('__WIKINDX__SPACE__', ' ', $value));
            $this->bibformat->{$type}[$key] = $value;
        }
        $this->loadArrays($type);
        $pString = $this->process($type);
        return $this->misc->b($this->messages->text('resourceType', $type) . ':') . $this->misc->br() . $pString;
    }

    /**
     * Process the example.
     */
    public function process(string $type)
    {
        // For WIKINDX, if type == book or book article and there exists both 'year1' and 'year2' in $row (entered as
        // publication year and reprint year respectively), then switch these around as 'year1' is
        // entered in the style template as 'originalPublicationYear' and 'year2' should be 'publicationYear'.
        if (($type == 'book') || ($type == 'book_article')) {
            $year2 = stripslashes($this->row['year2']);
            if ($year2 && !$this->row['year1']) {
                $this->row['year1'] = $year2;
                unset($this->row['year2']);
            } elseif ($year2 && $this->row['year1']) {
                $this->row['year2'] = stripslashes($this->row['year1']);
                $this->row['year1'] = $year2;
            }
        }
        $this->row = $this->bibformat->preProcess($type, $this->row);
        // Return $type is the OSBib resource type ($this->book, $this->web_article etc.) as used in Stylemap
        $type = $this->bibformat->getType();
        // Various types of creator
        for ($index = 1; $index <= 5; $index++) {
            $nameType = 'creator' . $index;
            if (array_key_exists($nameType, $this->bibformat->getStyleMap()->$type)) {
                $this->bibformat->formatNames($this->$nameType, $nameType);
            }
        }
        // The title of the resource
        $this->createTitle();
        // edition
        if ($editionKey = array_search('edition', $this->bibformat->getStyleMap()->$type)) {
            $this->createEdition($editionKey);
        }
        // pageStart and pageEnd
        $this->pages = false; // indicates not yet created pages for articles
        if (array_key_exists('pages', $this->bibformat->getStyleMap()->$type)) {
            $this->createPages();
        }
        // Date
        if (array_key_exists('date', $this->bibformat->getStyleMap()->$type)) {
            $this->createDate();
        }
        // runningTime for film/broadcast
        if (array_key_exists('runningTime', $this->bibformat->getStyleMap()->$type)) {
            $this->createRunningTime();
        }
        // web_article URL
        if (array_key_exists('URL', $this->bibformat->getStyleMap()->$type) &&
            ($itemElement = $this->createUrl())) {
            $this->bibformat->addItem($itemElement, 'URL');
        }
        // the rest...  All other database resource fields that do not require special formatting/conversion.
        $this->bibformat->addAllOtherItems($this->row);
        // We now have an array for this item where the keys match the key names of $this->styleMap->$type
        // where $type is book, journal_article, thesis etc. and are now ready to map this against the defined
        // bibliographic style for each resource ($this->book, $this->book_article etc.).
        // This bibliographic style array not only provides the formatting and punctuation for each field but also
        // provides the order. If a field name does not exist in this style array, we print nothing.
        $pString = $this->bibformat->map();
        // ordinals such as 5$^{th}$
        $pString = preg_replace_callback("/(\d+)\\$\^\{(.*)\}\\$/", [$this, 'ordinals'], $pString);
        // remove extraneous {...}
        return preg_replace('/{(.*)}/U', '$1', $pString);
    }

    /**
     * callback for ordinals above
     */
    public function ordinals(array $matches): string
    {
        return $matches[1] . '<sup>' . $matches[2] . '</sup>';
    }

    /**
     * Create the resource title
     */
    public function createTitle(): void
    {
        $pString = stripslashes($this->row['noSort'] ?? '') . ' ' .
            stripslashes($this->row['title'] ?? '');
        if ($this->row['subtitle'] ?? false) {
            $pString .= $this->bibformat->getTitleSubtitleSeparator() . stripslashes($this->row['subtitle']);
        }
        // anything enclosed in {...} is to be left as is
        $this->bibformat->formatTitle($pString, '{', '}');
    }

    /**
     * Create the URL
     */
    public function createUrl(): string
    {
        if (!($this->row['url'] ?? false)) {
            return '';
        }
        $url = htmlspecialchars(stripslashes($this->row['url']));
        unset($this->row['url']);
        return $this->misc->a('rLink', $url, $url, '_blank');
    }

    /**
     * Create date
     */
    public function createDate(): void
    {
        $startDay = isset($this->row['miscField2']) ? stripslashes($this->row['miscField2']) : false;
        $startMonth = isset($this->row['miscField3']) ? stripslashes($this->row['miscField3']) : false;
        unset($this->row['miscField2']);
        unset($this->row['miscField3']);
        $endDay = isset($this->row['miscField5']) ? stripslashes($this->row['miscField5']) : false;
        $endMonth = isset($this->row['miscField6']) ? stripslashes($this->row['miscField6']) : false;
        unset($this->row['miscField5']);
        unset($this->row['miscField6']);
        $startDay = ($startDay == 0) ? false : $startDay;
        $startMonth = ($startMonth == 0) ? false : $startMonth;
        if (!$startMonth) {
            return;
        }
        $endDay = ($endDay == 0) ? false : $endDay;
        $endMonth = ($endMonth == 0) ? false : $endMonth;
        $this->bibformat->formatDate($startDay, $startMonth, $endDay, $endMonth);
    }

    /**
     * Create runningTime for film/broadcast
     */
    public function createRunningTime(): void
    {
        $minutes = stripslashes($this->row['miscField1']);
        $hours = stripslashes($this->row['miscField4']);
        if (!$hours && !$minutes) {
            return;
        }
        if (!$hours) {
            $hours = 0;
        }
        $this->bibformat->formatRunningTime($minutes, $hours);
    }

    /**
     * Create the edition number
     */
    public function createEdition(string $editionKey)
    {
        if (!$this->row[$editionKey]) {
            return false;
        }
        $edition = stripslashes($this->row[$editionKey]);
        $this->bibformat->formatEdition($edition);
    }

    /**
     * Create page start and page end
     */
    public function createPages(): void
    {
        if (!$this->row['pageStart'] || $this->pages) { // empty field or page format already done
            $this->pages = true;
            return;
        }
        $this->pages = true;
        $start = trim(stripslashes($this->row['pageStart']));
        $end = $this->row['pageEnd'] ? trim(stripslashes($this->row['pageEnd'])) : false;
        $this->bibformat->formatPages($start, $end);
    }

    /**
    * Example values for  resources and creators
    */
    public function loadArrays(string $type): void
    {
        // Some of these default values may be overridden depending on the resource type.
        // The values here are the keys of resource type arrays in Stylemap.php
        $this->row = [
            'noSort'			=>				'The',
            'title' 			=>				'{OSBib System}',
            'subtitle'			=>				'Bibliographic formatting as it should be',
            'year1'				=>				'2003', // publicationYear
            'year2'				=>				'2004', // reprintYear
            'year3'				=>				'2001-2003', // volume set publication year(s)
            'pageStart'			=>				'109',
            'pageEnd'			=>				'122',
            'miscField2'		=>				'21', // start day
            'miscField3'		=>				'8', // start month
            'miscField4'		=>				'12', // numberOfVolumes
            'field1'			=>				'The Software Series', // seriesTitle
            'field2'			=>				'3', // edition
            'field3'			=>				'9', // seriesNumber
            'field4'			=>				'III', // volumeNumber
            'field5'			=>				'35', // umber
            'url'				=>				'http://bibliophile.sourceforge.net',
            'isbn'				=>				'0-9876-123456',
            'publisherName'		=>				'Botswana Books',
            'publisherLocation'	=>				'Selebi Phikwe',
            'collectionTitle'	=>				'The Best of Open Source Software',
            'collectionTitleShort'	=>			'Best_OSS',
        ];
        $authors = [
                    0 => [
                            'surname'		=>			'Grimshaw',
                            'firstname'		=>			'Mark',
                            'initials'		=>			'N',
                            'prefix'		=>			'',
                            ],
                    1 => [
                            'surname'		=>			'Boulanger',
                            'firstname'		=>			'Christian',
                            'initials'		=>			'',
                            'prefix'		=>			'',
                            ],
                    2 => [
                            'surname'		=>			'Rossato',
                            'firstname'		=>			'Andrea',
                            'initials'		=>			'',
                            'prefix'		=>			'',
                            ],
                    4 => [
                            'surname'		=>			'Guillaume',
                            'firstname'		=>			'Gardey',
                            'initials'		=>			'',
                            'prefix'		=>			'',
                            ],
                    ];
        $editors = [
                    0 => [
                            'surname'		=>			'Mouse',
                            'firstname'		=>			'Mickey',
                            'initials'		=>			'',
                            'prefix'		=>			'',
                            ],
                    1 => [
                            'surname'		=>			'Duck',
                            'firstname'		=>			'Donald',
                            'initials'		=>			'D D',
                            'prefix'		=>			'de',
                            ],
                    ];
        $revisers = [
                    0 => [
                            'surname'		=>			'Bush',
                            'firstname'		=>			'George',
                            'initials'		=>			'W',
                            'prefix'		=>			'',
                            ],
                    ];
        $translators = [
                    0 => [
                            'surname'		=>			'Lenin',
                            'firstname'		=>			'V I',
                            'initials'		=>			'',
                            'prefix'		=>			'',
                            ],
                    ];
        $seriesEditors = [
                    0 => [
                            'surname'		=>			'Freud',
                            'firstname'		=>			'S',
                            'initials'		=>			'',
                            'prefix'		=>			'',
                            ],
                    ];
        $composers = [
                    0 => [
                            'surname'		=>			'Mozart',
                            'firstname'		=>			'Wolfgang Amadeus',
                            'initials'		=>			'',
                            'prefix'		=>			'',
                            ],
                    ];
        $performers = [
                    0 => [
                            'surname'		=>			'Led Zeppelin',
                            'firstname'		=>			'',
                            'initials'		=>			'',
                            'prefix'		=>			'',
                            ],
                    ];
        $artists = [
                    0 => [
                            'surname'		=>			'Vinci',
                            'firstname'		=>			'Leonardo',
                            'initials'		=>			'',
                            'prefix'		=>			'da',
                            ],
                    ];
        $this->creator1 = $authors;
        $this->creator2 = $editors;
        $this->creator3 = $revisers;
        $this->creator4 = $translators;
        $this->creator5 = $seriesEditors;
        // For various types, override default settings above
        if ($type == 'genericMisc') {
            $this->row['field2'] = 'software';
            $this->row['subtitle'] = '';
            $this->row['publisherName'] = 'Kalahari Soft';
        } elseif ($type == 'magazine_article') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = '{OSS} Between the Sheets';
            $this->row['collectionTitle'] = 'The Scandal Rag';
            $this->row['collectionTitleShort'] = 'RAG';
            $this->row['field2'] = 'interview';
            $this->row['field4'] = 'Winter';
            $this->row['miscField5'] = '27'; // end day
            $this->row['miscField6'] = '8'; // end month
        } elseif ($type == 'journal_article') {
            $this->row['field1'] = '23'; // volume number
            $this->row['miscField6'] = '9'; // end month
        } elseif ($type == 'newspaper_article') {
            $this->row['field1'] = 'G2'; // section
            $this->row['field2'] = 'Gabarone';
            $this->row['collectionTitle'] = 'TseTswana Times';
            $this->row['collectionTitleShort'] = 'TsTimes';
        } elseif ($type == 'proceedings') {
            $this->row['publisherName'] = 'International Association of Open Source Software';
            $this->row['publisherLocation'] = 'Serowe';
            $this->row['miscField5'] = '3'; // end day
            $this->row['miscField6'] = '9'; // end month
        } elseif ($type == 'conference_paper') {
            $this->row['publisherName'] = 'International Association of Open Source Software';
            $this->row['publisherLocation'] = 'Serowe';
        } elseif ($type == 'proceedings_article') {
            $this->row['publisherName'] = 'International Association of Open Source Software';
            $this->row['publisherLocation'] = 'Serowe';
            $this->row['field4'] = '12'; // volume No.
            $this->row['miscField5'] = '3'; // end day
            $this->row['miscField6'] = '9'; // end month
            $this->row['collectionTitle'] = '7th. International OSS Conference';
            $this->row['collectionTitleShort'] = '7_IntOSS';
        } elseif ($type == 'thesis') {
            $this->row['field1'] = 'PhD';
            $this->row['field2'] = 'thesis';
            $this->row['field5'] = 'Pie in the Sky'; // Dept.
            $this->row['publisherName'] = 'University of Bums on Seats';
            $this->row['publisherLocation'] = 'Laputia';
        } elseif ($type == 'web_article') {
            $this->row['field1'] = '23';
        } elseif ($type == 'film') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'Kill Will Vol. 3';
            $this->row['publisherName'] = 'Totally Brain Dead Films';
            $this->row['publisherLocation'] = '';
            $this->row['field1'] = 'USA';
            $this->row['miscField1'] = '59'; // minutes
            $this->row['miscField4'] = '5'; // hours
        } elseif ($type == 'broadcast') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'We put people on TV and humiliate them';
            $this->row['publisherName'] = 'Lowest Common Denominator Productions';
            $this->row['publisherLocation'] = 'USA';
            $this->row['miscField1'] = '45'; // minutes
            $this->row['miscField4'] = ''; // hours
        } elseif ($type == 'music_album') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = 'Canon & Gigue';
            $this->row['title'] = 'Pachelbel';
            $this->row['isbn'] = '447-285-2';
            $this->row['publisherName'] = 'Archiv';
            $this->row['field2'] = 'CD'; // medium
            $this->row['year1'] = '1982-1983';
        } elseif ($type == 'music_track') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'Dazed and Confused';
            $this->row['collectionTitle'] = 'Led Zeppelin 1';
            $this->row['collectionTitleShort'] = 'LZ1';
            $this->row['isbn'] = '7567826322';
            $this->row['publisherName'] = 'Atlantic';
            $this->row['field2'] = 'CD'; // medium
            $this->row['year1'] = '1994';
        } elseif ($type == 'music_score') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'Sonata in A Minor';
            $this->row['isbn'] = '3801 05945';
            $this->row['publisherName'] = 'Alfred Publishing';
            $this->row['publisherLocation'] = 'New York';
            $this->row['year1'] = '1994';
        } elseif ($type == 'artwork') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'Art? What Art?';
            $this->row['publisherName'] = 'More Money than Sense';
            $this->row['publisherLocation'] = 'New York';
            $this->row['field2'] = 'Movement in protoplasma';
            $this->creator1 = $artists;
        } elseif ($type == 'software') {
            $this->row['field2'] = 'PHP source code'; // type
            $this->row['field4'] = '1.3'; // version
            $this->row['publisherName'] = 'Kalahari Soft';
            $this->row['publisherLocation'] = 'Maun';
        } elseif ($type == 'audiovisual') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'Whispering Sands';
            $this->row['field1'] = 'Chobe ArtWorks Series'; // series title
            $this->row['field2'] = 'video installation'; //medium
            $this->row['field4'] = 'IV'; // series number
            $this->row['publisherName'] = 'Ephemera';
            $this->row['publisherLocation'] = 'Maun';
            $this->creator1 = $artists;
        } elseif ($type == 'database') {
            $this->row['noSort'] = 'The';
            $this->row['subtitle'] = 'Sotware Listings';
            $this->row['title'] = 'Blue Pages';
            $this->row['publisherName'] = 'Kalahari Soft';
            $this->row['publisherLocation'] = 'Maun';
        } elseif ($type == 'government_report') {
            $this->row['noSort'] = 'The';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'State of Things to Come';
            $this->row['field1'] = 'Prognostications'; // section
            $this->row['field2'] = 'Pie in the Sky'; // department
            $this->row['publisherName'] = 'United Nations';
        } elseif ($type == 'hearing') {
            $this->row['field1'] = 'Committee on Unworldly Activities'; // committee
            $this->row['field2'] = 'United Nations'; // legislative body
            $this->row['field3'] = 'Summer'; //session
            $this->row['field4'] = '113'; // document number
            $this->row['miscField4'] = '27'; // no. of volumes
        } elseif ($type == 'statute') {
            $this->row['field1'] = '101.43a'; // public law no.
            $this->row['field2'] = 'Lex Hammurabi'; // code
            $this->row['field3'] = 'Autumn'; //session
            $this->row['field4'] = '34-A'; // section
            $this->row['year1'] = '1563 BC';
        } elseif ($type == 'legal_ruling') {
            $this->row['noSort'] = 'The';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'People v. George';
            $this->row['field1'] = 'Court of Public Law'; // section
            $this->row['field2'] = 'Appellate Decision'; // type
            $this->row['publisherName'] = 'Legal Pulp';
            $this->row['publisherLocation'] = 'Gabarone';
        } elseif ($type == 'case') {
            $this->row['noSort'] = 'The';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'People v. George';
            $this->row['field1'] = 'Public Law'; // reporter
            $this->row['field4'] = 'XIV'; // reporter volume
            $this->row['publisherName'] = 'Supreme Court';
        } elseif ($type == 'bill') {
            $this->row['noSort'] = 'The';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'People v. George';
            $this->row['field1'] = 'Court of Public Law'; // section
            $this->row['field2'] = 'Lex Hammurabi'; // code
            $this->row['field4'] = 'Spring'; // session
            $this->row['publisherName'] = 'United Nations';
            $this->row['publisherLocation'] = 'New York';
        } elseif ($type == 'patent') {
            $this->row['field1'] = 'Journal of Patents'; // publishedSource
            $this->row['field3'] = '289763[e].x-233'; // application no.
            $this->row['field4'] = 'bibliographic software'; // type
            $this->row['field5'] = '5564763[E].X-233'; // int. pat. no.
            $this->row['field6'] = 'OSBib'; // int. title
            $this->row['field7'] = 'software'; // int. class
            $this->row['field8'] = '0-84784-AAH.z'; // pat. no.
            $this->row['field9'] = 'not awarded'; // legal status
            $this->row['publisherName'] = 'Lawyers Inc.'; // assignee
            $this->row['publisherLocation'] = 'New Zealand';
        } elseif ($type == 'personal') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'Save up to 80% on Microsoft Products!';
            $this->row['field2'] = 'email'; // type
        } elseif ($type == 'unpublished') {
            $this->row['field2'] = 'manuscript'; // type
            $this->row['publisherName'] = 'University of Bums on Seats';
            $this->row['publisherLocation'] = 'Laputia';
        } elseif ($type == 'classical') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'Sed quis custodiet ipsos custodes?';
            $this->row['field4'] = 'Codex XIX'; // volume
            $this->row['year1'] = '114 BC'; // volume
        } elseif ($type == 'manuscript') {
            $this->row['field2'] = 'manuscript'; // type
            $this->row['publisherName'] = 'University of Bums on Seats';
            $this->row['publisherLocation'] = 'Laputia';
        } elseif ($type == 'map') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'Mappa Mundi';
            $this->row['field1'] = 'Maps of the World'; // series title
            $this->row['field2'] = 'isomorphic projection'; // type
        } elseif ($type == 'chart') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'Incidence of Sniffles in the New York Area';
            $this->row['field1'] = 'sniff_1.gif'; // filename
            $this->row['field2'] = 'The GIMP'; // program
            $this->row['field3'] = '800*600'; // size
            $this->row['field4'] = 'GIF'; // type
            $this->row['field5'] = '1.1a'; // version
            $this->row['field6'] = '11'; // number
            $this->row['publisherName'] = 'University of Bums on Seats';
            $this->row['publisherLocation'] = 'Laputia';
        } elseif ($type == 'miscellaneous') {
            $this->row['noSort'] = '';
            $this->row['subtitle'] = '';
            $this->row['title'] = 'Making Sunlight from Cucumbers';
            $this->row['field2'] = 'thin air'; // medium
            $this->row['publisherName'] = 'University of Bums on Seats';
            $this->row['publisherLocation'] = 'Laputia';
        }
    }
}
