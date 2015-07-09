#!/usr/bin/php
<?php
class test
{
    private $TESTVAR = 'NANITES';
    function thash($str, $salt = $this->TESTVAR)
    {
        echo $salt.n;
        return sha1($salt.$str);
    }
}
$t = new test;
$t->thash('hello world', 'grrgoons').n;
$t->thash('hello world');
?>