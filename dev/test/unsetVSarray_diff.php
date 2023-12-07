<?php
declare(strict_types=1);

$isHidden ? $aliasesList = &$this->hiddenAliases : $aliasesList = &$this->showAliases;
foreach ($aliases as $alias) {
	unset($aliasesList[$alias]);
}
if ($isHidden) {
	$this->hiddenAliases = array_diff($this->hiddenAliases, $aliases);
} else {
	$this->showAliases = array_diff($this->showAliases, $aliases);
}

$test = 100;
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