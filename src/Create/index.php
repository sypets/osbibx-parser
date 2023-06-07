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
 */

// Path to where the XML style files are kept.
$styleDir = '../styles/bibliography';

/**
* Initialise
*/
$errors = new Errors();
$init = new Init();
// Get user input in whatever form
$vars = $init->getVars();
// start the session
$init->startSession();

if (!$vars) {
    $admin = new Adminstyle($vars, $styleDir);
    $pString = $admin->gateKeep('display');
} elseif ($vars['action'] == 'adminStyleAddInit') {
    $admin = new Adminstyle($vars, $styleDir);
    $pString = $admin->gateKeep('addInit');
} elseif ($vars['action'] == 'adminStyleAdd') {
    $admin = new Adminstyle($vars, $styleDir);
    $pString = $admin->gateKeep('add');
} elseif ($vars['action'] == 'adminStyleEditInit') {
    $admin = new Adminstyle($vars, $styleDir);
    $pString = $admin->gateKeep('editInit');
} elseif ($vars['action'] == 'adminStyleEditDisplay') {
    $admin = new Adminstyle($vars, $styleDir);
    $pString = $admin->gateKeep('editDisplay');
} elseif ($vars['action'] == 'adminStyleEdit') {
    $admin = new Adminstyle($vars, $styleDir);
    $pString = $admin->gateKeep('edit');
} elseif ($vars['action'] == 'adminStyleCopyInit') {
    $admin = new Adminstyle($vars, $styleDir);
    $pString = $admin->gateKeep('copyInit');
} elseif ($vars['action'] == 'adminStyleCopyDisplay') {
    $admin = new Adminstyle($vars, $styleDir);
    $pString = $admin->gateKeep('copyDisplay');
} elseif ($vars['action'] == 'previewStyle') {
    $preview = new Previewstyle($vars, $styleDir);
    $pString = $preview->display();
    new Closepopup($pString);
} elseif ($vars['action'] == 'help') {
    $help = new Helpstyle();
    $pString = $help->display();
    new Close($pString, false);
} else {
    $pString = $errors->text('inputError', 'invalid');
}

/**
 * Close the HTML code by calling the constructor of Close which also
 * prints the HTTP header, body and flushes the print buffer.
*/
new Close($pString);
