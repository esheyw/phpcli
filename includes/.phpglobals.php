<?php
define ("MYSQL_SERVER", "localhost");
define ("MYSQL_USER", 'root');
define ("MYSQL_PW", 'manquo64');
define ('EVE_FUZZDUMP', 'https://www.fuzzwork.co.uk/dump/');
define ("EVE_SDE", file_get_contents('/root/.sdename'));
define ('n', "\n");

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