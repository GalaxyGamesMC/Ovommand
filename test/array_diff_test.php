<?php
declare(strict_types=1);

$values1 = [
    "hello" => 0,
    "hi" => 1,
    "cat" => 2,
    "dog" => 3,
    "foo" => 4,
    "bar" => 5
];

$values2 = array_keys($values1);

$removes = ["hi", "foo", "meow"];

$isBinding = true;

//if ($isBinding) {
    foreach ($removes as $remove) {
        if (isset($values1[$remove])) {
            unset($values1[$remove]);
        }
    }
//} else {
    $tempV2 = $values2;
    $start = microtime(true);
    $values2 = array_diff($values2, $removes);
    $total1 = microtime(true) - $start;

    $start = microtime(true);
    foreach ($tempV2 as $k) {
        if (isset($remove[$k])) {
            unset($tempV2[$k]);
        }
    }
    $updates = array_intersect($this->values, $)//,a ,dadahkdba
    $tempV2 = array_diff($tempV2, $removes);
    $total2 = microtime(true) - $start;

//}
var_dump($values1);
var_dump($values2);
var_dump($tempV2);

echo "temp 1: " . $total1 . "\n";
echo "temp 2: " . $total2 . "\n";
