<?php
declare(strict_types=1);
require_once "D:\phpstorm2\Ovommand\src\galaxygames\ovommand\utils\Utils.php";
use galaxygames\ovommand\utils\Utils;

function vd(mixed $var) : void{
    var_dump($var);
}

$arr1 = ["1", "2", 3, 4, 5, true, "meoww", new stdClass];

$arr2 = ["1" => 2, "3" => 4, "new" => true, true => new stdClass()];

//vd(Utils::dumpForceStringList($arr1));
vd(Utils::dumpForceStringArray($arr2));
vd(Utils::dumpForceStringArray2($arr2));