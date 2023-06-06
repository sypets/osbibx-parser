<?php
declare(strict_types=1);
namespace Sypets\OsbibxParser\Create;
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
 * Miscellaneous HTML Form processing
 *
 * @author Mark Grimshaw
 *
 * $Header: /cvsroot/bibliophile/OSBib/create/Formmisc.php,v 1.1 2005/06/20 22:26:51 sirfragalot Exp $
*/
class Formmisc
{
    /**
     * reduce the size of long text (in select boxes usually) to keep web browser display tidy
     * optional $override allows the programmer to override the user set preferences
     */
    public function reduceLongText(string $text, int $limit = 40): string
    {
        if (($limit != -1) && ($count = preg_match_all('/./', $text, $throwAway)) > $limit) {
            $start = floor(($limit/2) - 2);
            $length = $count - (2 * $start);
            $text = substr_replace($text, ' ... ', $start, $length);
        }
        return $text;
    }
}