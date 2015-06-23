#!/usr/bin/php
<?php
#globals
$remotemd5 = null;
$tarball = null;
$oldsdename = null;
$newsdeline = null;
$newsdename = null;
$sqlfile = null;
$wget_output = null;
$wget_return_code = null;
$download = true;

#db connect
$myc = new mysqli(
        MYSQL_SERVER,
        MYSQL_USER,
        MYSQL_PW
    );
if ($myc->connect_errno) {
    echo "Failed to connect to MySQL: (" . $myc->connect_errno . ") " . $myc->connect_error;
}

#find current SDE name
$dblist = $myc->query("show databases;");

foreach ($dblist as $row) 
{
    if (stripos(current($row), 'sde') !== false)
    {
        $oldsdename = current($row);
    }
}

#pull the entire fuzzwork SDE page
$fuzzdump = file(EVE_FUZZDUMP);
#find the line with the new SDE name on it
$newsdeline = implode(preg_grep('%(sde\w+)-(\d+)\.(\d+)%', $fuzzdump));
#get new sde name in format "sde{name}{majorver}{minorver}" in $newsdematches[1..3]
preg_match('%(sde\w+)-(\d+)\.(\d+)-\d+%', $newsdeline, $newsdematches);
#get folder name that will be extracted from the tarball
$newsdefolder = array_shift($newsdematches);
$newsdename = implode($newsdematches);

if (!isset($oldsdename)) 
{
    $createnewsde = ask('No current SDE found. Continue?', true);
    if ($createnewsde)
    {
        echo 'Creating database '.$newsdename.' fresh...'.n;
        $myc->query('create database '.$newsdename.';');
    }
    else
    {
        sdie('Aborting.'.n);
    }
}
else
{
    echo 'Current SDE: '.$oldsdename.n.'New SDE: '.$newsdename.n;
}


#get filenames from fuzzwork that contain 'latest'

$latestlines = preg_grep('%mysql-latest%', $fuzzdump);

foreach ($latestlines as $sdefile)
{
    #find 1) th
    preg_match('%>(.+)</a>\h+(\d+-\d+-\d+ \d+:\d+)%', $sdefile, $sdematches);
    if (stripos($sdematches[1], 'md5') !== false)
    {
        $md5file = $sdematches[1];
    }
    else
    {
        $tarball = $sdematches[1];
    }
}

#See if update is even necessary
if ($oldsdename == $newsdename)
{
    $overwrite = ask('SDE names match. Overwrite?', true);
    if (!$overwrite) sdie('Aborting.'.n);
}

#if we have an existing copy of the current sde tarball, replace it?
if (file_exists($tarball))
{
    $download = ask('Local tarball exists, delete and redownload?', true);
    if ($download)
    {
        echo 'Deleting local tarball...'.n;
        unlink ($tarball);
    }
    else
    {
        echo 'Using existing tarball...'.n;
    }
}

# $download defaults to true if there was no existing local tarball
if ($download)
{
    echo 'Retreiving remote tarball...'.n;
    exec('wget '.EVE_FUZZDUMP.$tarball, $wget_output, $wget_return_code);
    if ($wget_return_code !== 0)
    {
        sdie('wget error ('.$wget_return_code.'): '.implode(n, $wget_output));
    }
    #md5 check the download
    $md5hash = (substr(file_get_contents(EVE_FUZZDUMP.$md5file),0,32));
    if (md5_file($tarball) != $md5hash)
    {
        unlink($tarball);
        sdie('Tarball MD5 check failed!'.n);
    }
    else
    {
        echo 'MD5 check successful.'.n;
    }
}
 
#unpack the tarball
if (file_exists($newsdefolder))
{
    $killfolder = ask('Existing SDE folder, remove?', true);
    if ($killfolder)
    {
        exec('rm -r '.$newsdefolder);
    }
}
echo 'Unpacking tarball...'.n;
#x for extract, j for bz2, f for file
exec ('tar xjf '.$tarball, $tar_output, $tar_return_code);
if ($tar_return_code !== 0)
{
    sdie ('tar error ('.$tar_return_code.'): '.implode(n, $tar_output));
}

#make sure we have the right database name
$myc->query ('DROP DATABASE IF EXISTS '.$oldsdename.';');
$myc->query ('CREATE DATABASE IF NOT EXISTS '.$newsdename.';');

#find the filename of the .sql file
$sdedir = dir($newsdefolder);
while (($sdefile = $sdedir->read()) !== false)
{
    if (stripos($sdefile, '.sql') !== false)
    {
        $sqlfile = $sdefile;
        break;
    }
}
$sdedir->close();

#do the actual importing
$cmd = 'mysql -u '.MYSQL_USER.' -p -D '.$newsdename.' < '.$newsdefolder.'/'.$sqlfile;
echo 'Importing .SQL file...'.n;
exec ($cmd);

#clean up the tarball and extracted folder
exec('rm -r '.$newsdefolder);
unlink($tarball);

#close remaining handles
$myc->close();
?>