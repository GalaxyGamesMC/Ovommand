<?php
declare(strict_types=1);

$s = "~1313~131~13a"; //worked great yay :D

preg_match_all("/(\S[~^]?[+-]?(?:\d*\.\d+|\d+)?)([^~\s\d]+)?/", $s, $matches);
var_dump($matches[1], $matches[2]);

$xType = $matches[1][0][0];
$xValue = substr($matches[1][0], 1);
$xInvalid = $matches[2][0];
if (!empty($xInvalid)) {
	throw new RuntimeException("$xInvalid in \$xInvalid");
}
$yType = $matches[1][1][0];
$yValue = substr($matches[1][1], 1);
$yInvalid = $matches[2][1];
if (!empty($yInvalid)) {
	throw new RuntimeException("$yInvalid in \$yInvalid");
}
$zType = $matches[1][2][0];
$zValue = substr($matches[1][2], 1);
$zInvalid = $matches[2][2];
if (!empty($zInvalid)) {
	throw new RuntimeException("$zInvalid in \$zInvalid");
}
/*
 C:\Users\nttis\Downloads\PocketMine-MP\bin\php\php.exe -c C:\Users\nttis\Downloads\PocketMine-MP\bin\php\php.ini D:\pmmp\Ovommand\test\preg_match_all.php
array(3) {
  [0]=>
  string(5) "~1313"
  [1]=>
  string(4) "~131"
  [2]=>
  string(3) "~13"
}
array(3) {
  [0]=>
  string(0) ""
  [1]=>
  string(0) ""
  [2]=>
  string(1) "a"
}

Fatal error: Uncaught RuntimeException: a in $zInvalid in D:\pmmp\Ovommand\test\preg_match_all.php:25
Stack trace:
#0 {main}
  thrown in D:\pmmp\Ovommand\test\preg_match_all.php on line 25

Process finished with exit code 255

 */