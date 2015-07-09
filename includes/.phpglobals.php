<?php
require_once "credentials.php";
require_once "definitions.php";
$_HANDLES = array();
#trim underscores, and only underscores, from a string
function trim_($str)
{
    return trim($str, '_');
}
function is_included ()
{
    return !($_SERVER['SCRIPT_FILENAME'] === __FILE__);
}
function ind($level = 1)
{
    return str_repeat(' ', ($level * 4));
}
#die with some degree of courtesy to open handles
function sdie($code)
{
    global $myc;
    if (is_resource($myc)) $myc->close();
    die($code);
}
if (PHP_SAPI === 'cli')
{
    require_once 'cliglobals.php';
}
if (PHP_SAPI === 'apache2handler')
{
    require_once 'webglobals.php';
}
if (extension_loaded('scrypt'))
{
    include_once 'scryptHash.php';
}
?>