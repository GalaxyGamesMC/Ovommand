<?php
declare(strict_types=1);
//require_once "D:\phpstorm2\Ovommand\src\galaxygames\ovommand\utils\Utils.php";
require_once "vendor/autoload.php";

[http‎s://youtu.be](<https://youtu.be/dQw4w9WgXcQ?si=jdTbpUXCuSPRJM2F>)
[https://youtu.be* *](<https://youtu.be/dQw4w9WgXcQ?si=jdTbpUXCuSPRJM2F>)
//use galaxygames\ovommand\utils\Utils;

function dumpStringList(array $arr) : array{
    $results = [];

    foreach ($arr as $v) {
        $results[] = convertToString($v);
    }
    return $results;
}

function convertToString(mixed $value) : string{
    if (is_null($value)) {
        return "null";
    }
    if (is_bool($value)) {
        return $value ? "true" : "false";
    }
    if (is_object($value)) {
        return "{#" . spl_object_id($value) . "}";
    }
    if (is_array($value)) {
        return "​" . implode(";", $value) . "​";
    }
    return (string) $value;
}

$arr1 = ["1", "2", 3, 4, 5, true, null, false, [], "meoww", new stdClass];
$arr2 = ["1" => 2, "3333333333" => 4, "new" => true, "eta" => new stdClass()];

dump(dumpStringList($arr1));
dump($arr1);