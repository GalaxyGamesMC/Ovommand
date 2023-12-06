<?php
declare(strict_types=1);

$test = 100;
$test = 1;
//$test = 1;
//$out = $test === 1;
$arr = [];
$temp = 0;
$rate = 0;
for ($i = 1; $i <= $test; ++$i) {
	$time = -hrtime(true);
	if (empty($arr)) {
		$temp = 0;
	}
	$time += hrtime(true);
	$rate += $time;
}
echo("Test1: " . sprintf('%0.4fns', $rate/$test) . "\n");
$rate = 0;
for ($i = 1; $i <= $test; ++$i) {
	$time = -hrtime(true);
	if ($arr === []) {
		$temp = 0;
	}
	$time += hrtime(true);
	$rate += $time;
}
echo("Test2: " . sprintf('%0.4fns', $rate/$test) . "\n");
$rate = 0;
for ($i = 1; $i <= $test; ++$i) {
	$time = -hrtime(true);
	if (count($arr) === 0) {
		$temp = 0;
	}
	$time += hrtime(true);
	$rate += $time;
}
echo("Test3: " . sprintf('%0.4fns', $rate/$test) . "\n");
echo("------------------=---------------\n");