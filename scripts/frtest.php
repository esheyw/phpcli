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
$frc->setHashFunc('scryhash');
$t  = $_EVEAPI['Thallius'];
$fr = new FluidRouter($t['keyID'], $t['vCode'], $frc);

#
echo $fr->setUserAgent('TEST').n;
#echo $fr->setUserAgent(42).n;
#$chars = $fr->query('account/Characters');
#var_dump($chars);
?>