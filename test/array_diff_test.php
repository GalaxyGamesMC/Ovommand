<?php
/**
 * Ovommand - A command virion for PocketMine-MP
 *                     ／l、
 *                   （ﾟ､ ｡ ７
 *                     l ~ヽ
 *  _____             じしf_,)ノ	           _
 * |     | _ _  ___  _____  _____  ___  ___ _| |
 * |  |  || | ||>_ ||     ||     || .'||   || . |
 * |_____| \_/ |___||_|_|_||_|_|_||__,||_|_||___|
 *
 * Copyright (C) 2023 GalaxyGamesMC
 * @link https://github.com/GalaxyGamesMC/Ovommand
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, @see <https://www.gnu.org/licenses/>.
 */

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
    $updates = array_intersect($this->values); //,a ,dadahkdba
    $tempV2 = array_diff($tempV2, $removes);
    $total2 = microtime(true) - $start;

//}
var_dump($values1);
var_dump($values2);
var_dump($tempV2);

echo "temp 1: " . $total1 . "\n";
echo "temp 2: " . $total2 . "\n";
