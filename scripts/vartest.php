#!/usr/bin/php
<?php
class test
{
    const TESTVAR = 'NANITES';
    function thash($str, $salt = self::TESTVAR)
    {
        echo $salt.n;

        return sha1($salt.$str);
    }
}
$t = new test;
$t->thash('hello world', 'grrgoons').n;
$t->thash('hello world');
?>