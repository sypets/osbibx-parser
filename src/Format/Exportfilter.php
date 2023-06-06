<?php

declare(strict_types=1);
namespace Sypets\OsbibxParser\Format;

/********************************
OSBib:
A collection of PHP classes to create and manage bibliographic formatting for OS bibliography software
using the OSBib standard.

Released through http://bibliophile.sourceforge.net under the GPL licence.
Do whatever you like with this -- some credit to the author(s) would be appreciated.

If you make improvements, please consider contacting the administrators at bibliophile.sourceforge.net
so that your improvements can be added to the release package.

Mark Grimshaw 2005
http://bibliophile.sourceforge.net
********************************/

/**
 * Description of class EXPORT
 * Format a bibliographic resource for output.
 *
 * @author Andrea Rossato
 * @version 1
 */
class Exportfilter
{
    protected string $newLine = '';
    protected ?FormatInterface $bibformat = null;
    protected $format;

    /**
     * $dir is the path to Stylemap.php etc.
     * @param FormatInterface $ref
     * @param string $output
     */
    public function __construct(FormatInterface &$ref, string $output)
    {
        $this->bibformat =&$ref;
        $this->format = $output;
        // New line (used in Citeformat::endnoteProcess)
        if ($this->format == 'rtf') {
            $this->newLine = '\\par\\qj ';
        } elseif ($this->format == 'html') {
            $this->newLine = '<br />';
        } else {
            $this->newLine = "\n";
        }
    }

    /**
     * @return string
     */
    public function getNewLine(): string
    {
        return $this->newLine;
    }

    /**
     * Format for HTML or RTF/plain?
     *
     * @author	Mark Grimshaw
     * @version	1
     *
     * @param $data Input string
     */
    public function format(string $data): string
    {
        if ($this->format == 'html') {
            /**
            * Scan for search patterns and highlight accordingly
            */
            /**
            * Temporarily replace any URL - works for just one URL in the output string.
            */
            if (preg_match("/(<a.*>.*<\/a>)/i", $data, $match)) {
                $url = preg_quote($match[1], '/');
                $data = preg_replace("/$url/", 'OSBIB__URL__OSBIB', $data);
            } else {
                $url = false;
            }
            $data = str_replace('"', '&quot;', $data);
            $data = str_replace('<', '&lt;', $data);
            $data = str_replace('>', '&gt;', $data);
            $data = preg_replace('/&(?![a-zA-Z0-9#]+?;)/', '&amp;', $data);
            $data = $this->bibformat->getPatterns() ?
                preg_replace(
                    $this->bibformat->getPatterns(),
                    '<span class="' . $this->bibformat->getPatternHighlight() . '">$1</span>',
                    $data
                ) : $data;
            $data = preg_replace("/\[b\](.*?)\[\/b\]/is", '<strong>$1</strong>', $data);
            $data = preg_replace("/\[i\](.*?)\[\/i\]/is", '<em>$1</em>', $data);
            $data = preg_replace("/\[sup\](.*?)\[\/sup\]/is", '<sup>$1</sup>', $data);
            $data = preg_replace("/\[sub\](.*?)\[\/sub\]/is", '<sub>$1</sub>', $data);
            $data = preg_replace(
                "/\[u\](.*?)\[\/u\]/is",
                '<span style="text-decoration: underline;">$1</span>',
                $data
            );
            // Recover any URL
            if ($url) {
                $data = str_replace('OSBIB__URL__OSBIB', $match[1], $data);
            }
            $data = str_replace('WIKINDX_NDASH', '&ndash;', $data);
        } elseif ($this->format == 'rtf') {
            $data = preg_replace('/&#(.*?);/', '\\u$1', $data);
            $data = preg_replace("/\[b\](.*?)\[\/b\]/is", '{{\\b $1}}', $data);
            $data = preg_replace("/\[i\](.*?)\[\/i\]/is", '{{\\i $1}}', $data);
            $data = preg_replace("/\[u\](.*?)\[\/u\]/is", '{{\\ul $1}}', $data);
            $data = preg_replace("/\[sup\](.*?)\[\/sup\]/is", '{{\\super $1}}', $data);
            $data = preg_replace("/\[sub\](.*?)\[\/sub\]/is", '{{\\sub $1}}', $data);
            $data = str_replace('WIKINDX_NDASH', "\\u8212\\'14 ", $data);
        }
        /**
        * OpenOffice-1.x.
        */
        elseif ($this->format == 'sxw') {
            $data = $this->bibformat->getUtf8()->decodeUtf8($data);
            $data = str_replace('"', '&quot;', $data);
            $data = str_replace('<', '&lt;', $data);
            $data = str_replace('>', '&gt;', $data);
            $data = preg_replace('/&(?![a-zA-Z0-9#]+?;)/', '&amp;', $data);
            $data = preg_replace("/\[b\](.*?)\[\/b\]/is", '<text:span text:style-name="textbf">$1</text:span>', $data);
            $data = preg_replace("/\[i\](.*?)\[\/i\]/is", '<text:span text:style-name="emph">$1</text:span>', $data);
            $data = preg_replace("/\[sup\](.*?)\[\/sup\]/is", '<text:span text:style-name="superscript">$1</text:span>', $data);
            $data = preg_replace("/\[sub\](.*?)\[\/sub\]/is", '<text:span text:style-name="subscript">$1</text:span>', $data);
            $data = preg_replace(
                "/\[u\](.*?)\[\/u\]/is",
                '<text:span text:style-name="underline">$1</text:span>',
                $data
            );
            $data = '<text:p text:style-name="Text body">' . $data . "</text:p>\n";
            $data = str_replace('WIKINDX_NDASH', '-', $data);
        }
        /**
        * 'noScan' means do nothing (leave BBCodes intact)
        */
        elseif ($this->format == 'noScan') {
            $data = str_replace('WIKINDX_NDASH', '-', $data);
            return $data;
        }
        /**
        * StripBBCode for 'plain'.
        */
        else {
            $data = preg_replace("/\[.*\]|\[\/.*\]/U", '', $data);
            $data = str_replace('WIKINDX_NDASH', '-', $data);
        }
        return $data;
    }
}
