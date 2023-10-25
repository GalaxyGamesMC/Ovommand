<?php
declare(strict_types=1);

$falseString = "§t sub1 aa§abcabcă â b c e ê ế bb";
var_dump($falseString);


const ESCAPE = "\xc2\xa7"; //§

function tokenize(string $string) : array{
	$result = preg_split("/(" . ESCAPE . "[0-9a-gk-or])/", $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	if($result === false) throw "NO";
	return $result;
}

var_dump(tokenize($falseString));