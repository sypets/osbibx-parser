<?php

declare(strict_types=1);
namespace Sypets\OsbibxParser\Parse;

/*
Released through http://bibliophile.sourceforge.net under the GPL licence.
Do whatever you like with this -- some credit to the author(s) would be appreciated.

A collection of PHP classes to manipulate bibtex files.

If you make improvements, please consider contacting the administrators at bibliophile.sourceforge.net so that your improvements can be added to the release package.

Mark Grimshaw 2004/2005
http://bibliophile.sourceforge.net

28/04/2005 - Mark Grimshaw.  Efficiency improvements.
*/
// For a quick command-line test (php -f Parsecreators.php) after installation, uncomment these lines:

/**
    $authors = "Mark N. Grimshaw and Bush III, G.W. & M. C. Hammer Jr. and von Frankenstein, Ferdinand Cecil, P.H. & Charles Louis Xavier Joseph de la Vallee Poussin";
    $creator = new Parsecreators();
    $creatorArray = $creator->parse($authors);
    print_r($creatorArray);
*/
class Parsecreators
{
    protected array $prefix = [];

    /** Create writer arrays from bibtex input.
     * 'author field can be (delimiters between authors are 'and' or '&'):
     * 1. <first-tokens> <von-tokens> <last-tokens>
     * 2. <von-tokens> <last-tokens>, <first-tokens>
     * 3. <von-tokens> <last-tokens>, <jr-tokens>, <first-tokens>
     *
     * @param string $input
     * @return array|bool
    */
    public function parse(string $input)
    {
        $input = trim($input);
        // split on ' and '
        $authorArray = preg_split("/\s(and|&)\s/i", $input);
        foreach ($authorArray as $value) {
            $appellation = $prefix = $surname = $firstname = $initials = '';
            $this->prefix = [];
            $author = explode(',', preg_replace("/\s{2,}/", ' ', trim($value)));
            $size = count($author);
            // No commas therefore something like Mark Grimshaw, Mark Nicholas Grimshaw, M N Grimshaw, Mark N. Grimshaw
            if ($size == 1) {
                // Is complete surname enclosed in {...}
                if (preg_match('/(.*){(.*)}/', $value, $matches)) {
                    $author = mb_split(' ', $matches[1]);
                    $surname = $matches[2];
                } else {
                    $author = mb_split(' ', $value);
                    // last of array is surname (no prefix if entered correctly)
                    $surname = array_pop($author);
                }
            }
            // Something like Grimshaw, Mark or Grimshaw, Mark Nicholas  or Grimshaw, M N or Grimshaw, Mark N.
            elseif ($size == 2) {
                // first of array is surname (perhaps with prefix)
                list($surname, $prefix) = $this->grabSurname(array_shift($author));
            }
            // If $size is 3, we're looking at something like Bush, Jr. III, George W
            else {
                // middle of array is 'Jr.', 'IV' etc.
                $appellation = implode(' ', array_splice($author, 1, 1));
                // first of array is surname (perhaps with prefix)
                list($surname, $prefix) = $this->grabSurname(array_shift($author));
            }
            $remainder = implode(' ', $author);
            list($firstname, $initials) = $this->grabFirstnameInitials($remainder);
            if (!empty($this->prefix)) {
                $prefix = implode(' ', $this->prefix);
            }
            $surname = $surname . ' ' . $appellation;
            $creators[] = ["$firstname", "$initials", "$surname", "$prefix"];
        }
        if (isset($creators)) {
            return $creators;
        }
        return false;
    }

    /**
     * grab firstname and initials which may be of form "A.B.C." or "A. B. C. " or " A B C " etc.
     */
    public function grabFirstnameInitials(string $remainder): array
    {
        $firstname = $initials = '';
        $array = mb_split(' ', $remainder);
        foreach ($array as $value) {
            $firstChar = mb_substr($value, 0, 1);
            if ((ord($firstChar) >= 97) && (ord($firstChar) <= 122)) {
                $this->prefix[] = $value;
            } elseif (preg_match('/[a-zA-Z]{2,}/', trim($value))) {
                $firstnameArray[] = trim($value);
            } else {
                $initialsArray[] = str_replace('.', ' ', trim($value));
            }
        }
        if (isset($initialsArray)) {
            foreach ($initialsArray as $initial) {
                $initials .= ' ' . trim($initial);
            }
        }
        if (isset($firstnameArray)) {
            $firstname = implode(' ', $firstnameArray);
        }
        return [$firstname, $initials];
    }

    /**
     * surname may have title such as 'den', 'von', 'de la' etc. - characterised by first character lowercased.  Any
     * uppercased part means lowercased parts following are part of the surname (e.g. Van den Bussche)
     */
    public function grabSurname(string $input): array
    {
        $surnameArray = mb_split(' ', $input);
        $noPrefix = $surname = false;
        foreach ($surnameArray as $value) {
            $firstChar = mb_substr($value, 0, 1);
            if (!$noPrefix && (ord($firstChar) >= 97) && (ord($firstChar) <= 122)) {
                $prefix[] = $value;
            } else {
                $surname[] = $value;
                $noPrefix = true;
            }
        }
        if ($surname) {
            $surname = implode(' ', $surname);
        }
        if (isset($prefix)) {
            $prefix = implode(' ', $prefix);
            return [$surname, $prefix];
        }
        return [$surname, false];
    }
}
