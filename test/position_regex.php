<?php
declare(strict_types=1);

$input_valid = "~+121212212 ^-1 -1.879a817381975";
//$in_error_4_groups = "~+121212212 ^-1 -1.879a817381975 131313";
//$input_valid = "~+121212212 ^-1 -1.879a817381975";
$reg = "*([~^]?)([+-]?[\d]?[\d.\d]+)*";

$matches = [];
preg_match_all($reg, $input_valid, $matches);

if (count($matches[0]) !== 3) {
    throw new \RuntimeException("Syntax error!");
}

// issues, no parse syntax errors!

print_r($matches);
var_dump($matches[2]);

$matches[2] = array_map(static fn(string $in) : int|float => str_contains($in, ".") ? (double) $in : (int) $in, $matches[2]);
var_dump($matches[2]);


echo(json_encode($matches, JSON_PRETTY_PRINT));

// https://3v4l.org/XX82L