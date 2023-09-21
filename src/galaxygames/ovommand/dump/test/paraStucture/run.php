<?php
declare(strict_types=1);

$arr = [
];

file_put_contents("out.json", json_encode($arr, JSON_PRETTY_PRINT));