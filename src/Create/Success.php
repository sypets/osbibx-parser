<?php
declare(strict_types=1);
namespace Sypets\OsbibxParser\Create;
use Sypets\OsbibxParser\Utf8;
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
* Success messages
*
* @author Mark Grimshaw
*
* $Header: /cvsroot/bibliophile/OSBib/create/Success.php,v 1.1 2005/06/20 22:26:51 sirfragalot Exp $
*/
class Success
{
    protected ?Misc $misc = null;
    protected ?Utf8 $utf8 = null;

    public function __construct()
    {
        $this->misc = new Misc();
        $this->utf8 = new Utf8();
    }

    /**
    * Print the message
    */
    public function text(string $indexName, string $extra = ''): string
    {
        $arrays = $this->loadArrays();
        $string = $arrays[$indexName];
        $string = $extra ? preg_replace('/###/', $this->utf8->smartUtf8_decode($extra), $string) :
            preg_replace('/###/', '', $string);
        return $this->misc->p($this->utf8->encodeUtf8($string), 'success', 'center');
    }

    /**
     * English success messages
     */
    public function loadArrays(): array
    {
        return [
            'style' => 'Successfully###bibliographic style',
        ];
    }
}
