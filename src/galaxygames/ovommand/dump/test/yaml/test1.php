<?php
declare(strict_types=1);

$str = "[x: 140, y: 64,z: -200, y: 12331, y: 2]";
//$str = ltrim($str, "[");
//$str = rtrim($str, "]");

$str = str_replace("=", ": ", $str);
var_dump($str);

var_dump(yaml_parse($str));