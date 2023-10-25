<?php
declare(strict_types=1);

$parameters = [
	"~1~~1",
	"2",
	"3"
   //012345678
];
$pars = [];
foreach ($parameters as $parameter) {
	$s = $parameter;
	$l = strlen($s);

	$currentPos = 0;

	while ($currentPos < $l) {
		$tildePos = strpos($s, "~", $currentPos + 1);
		$caretPos = strpos($s, "^", $currentPos + 1);
		if ($tildePos === false) {
			$tildePos = $l;
		}
		if ($caretPos === false) {
			$caretPos = $l;
		}
		$nextPos = min($tildePos, $caretPos);

		$pars[] = substr($s, $currentPos, $nextPos - $currentPos);
		$currentPos = $nextPos;
	}
}
var_dump($pars);