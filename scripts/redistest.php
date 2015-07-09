#!/usr/bin/php
<?php
require_once 'FluidRouter.php';
require_once 'FluidRouterRedisCache.php';
function scryhash($str, $salt)
{
    return scryptHash::hash($str, $salt);
}



$r = new Redis();
$r->connect('localhost');

$frc = new FluidRouterRedisCache($r);
#$frc->setHashFunc('scryhash');
$fr = new FluidRouter($APIKEY, $APIVCODE, $frc);

#
#echo $fr->hash('hello world').n;
$chars = $fr->query('account/Characters');
#var_dump($chars);
?>