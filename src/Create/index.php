<?php
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
 * index.php
 * @author Mark Grimshaw
 *
 * $Header: /cvsroot/bibliophile/OSBib/create/index.php,v 1.2 2005/06/25 02:57:34 sirfragalot Exp $
 *
 */

// Path to where the XML style files are kept.
define("OSBIB_STYLE_DIR", "../styles/bibliography");


/**
* Initialise
*/
$errors = new Errors();
$init = new Init();
// Get user input in whatever form
$vars = $init->getVars();
// start the session
$init->startSession();

if(!$vars)
{
    $admin = new Adminstyle($vars);
    $pString = $admin->gateKeep('display');
}
else if($vars["action"] == 'adminStyleAddInit')
{
    $admin = new Adminstyle($vars);
    $pString = $admin->gateKeep('addInit');
}
else if($vars["action"] == 'adminStyleAdd')
{
    $admin = new Adminstyle($vars);
    $pString = $admin->gateKeep('add');
}
else if($vars["action"] == 'adminStyleEditInit')
{
    $admin = new Adminstyle($vars);
    $pString = $admin->gateKeep('editInit');
}
else if($vars["action"] == 'adminStyleEditDisplay')
{
    $admin = new Adminstyle($vars);
    $pString = $admin->gateKeep('editDisplay');
}
else if($vars["action"] == 'adminStyleEdit')
{
    $admin = new Adminstyle($vars);
    $pString = $admin->gateKeep('edit');
}
else if($vars["action"] == 'adminStyleCopyInit')
{
    $admin = new Adminstyle($vars);
    $pString = $admin->gateKeep('copyInit');
}
else if($vars["action"] == 'adminStyleCopyDisplay')
{
    $admin = new Adminstyle($vars);
    $pString = $admin->gateKeep('copyDisplay');
}

else if($vars["action"] == 'previewStyle')
{
    $preview = new Previewstyle($vars);
    $pString = $preview->display();
    new Closepopup($pString);
}

else if($vars["action"] == 'help')
{
    $help = new Helpstyle();
    $pString = $help->display();
    new Close($pString, FALSE);
}
else {
    $pString = $errors->text("inputError", "invalid");
}

/**
 * Close the HTML code by calling the constructor of Close which also
 * prints the HTTP header, body and flushes the print buffer.
*/
new Close($pString);


