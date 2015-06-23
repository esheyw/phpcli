<?php
include "credentials.php";
include "definitions.php";

#trim underscores, and only underscores, from a string
function trim_($str)
{
    return trim($str, '_');
}

#die with some degree of courtesy to open handles
function sdie($code)
{
    global $myc;
    if (is_resource($myc)) $myc->close();
    die($code);
}
if (php_sapi_name() === 'cli')
{
    include 'cliglobals.php';
}
?>