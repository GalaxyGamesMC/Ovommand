<?php
declare(strict_types=1);

require_once "D:\pmmp\Ovommand\\vendor\autoload.php";

use galaxygames\ovommand\parameter\PositionParameter;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;

$parameter = new PositionParameter("t");

$test = 10000;
//$test = 1;
$out = $test === 1;

$parameters = [
	["~131318763183~-131873173", "~11232213123213123123213213763172791281631287162317863781.1321368763287812"],
	["~~~"],
	["~~", "~"],
	["^^^a"],
	["~131318763183~-131873173", "~1123221312321312312321321376317a2791281631287162317863781.1321368763287812"],
	["~131318763183~-131873173", "^+11232213123213123123213213763aaa"],
	["~13131", "~+1123221312321312312", "-13132313133"],
	["13131", "+1123221312321312312", "-13132313133"],
	[""],
	[" ", "  "],
];

$tz = 'GMT+7';
$timestamp = time();
try {
	$dt = new DateTime("now", new DateTimeZone($tz));
	$dt->setTimestamp($timestamp);
	echo "Test with $test entries, " . count($parameters) . " inputs, " . $dt->format("h:i d/m/Y T") . "\n\n";
} catch (Exception $e) {
	echo $e->getMessage();
}

foreach ($parameters as $params) {
	echo "\"" . implode(" ", $params) . "\"" . "\n";
	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		$result = $parameter->parse($params);
		$end = microtime(true);
		if ($end - $start == 0) {
			$i--;
		}
		$rate += $end-$start;
	}
	if ($out && $result instanceof BrokenSyntaxResult) {
		echo $result->getBrokenSyntax() . "\n";
	}
	echo("Test Legacy: " . sprintf('%0.25f', $rate/$test) . PHP_EOL);
	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		$result = @$parameter->betaParse($params);
		$end = microtime(true);
		if ($end - $start == 0) {
			$i--;
		}
		$rate += $end-$start;
	}
	if ($out && $result instanceof BrokenSyntaxResult) {
		echo $result->getBrokenSyntax() . "\n";
	}
	echo("Test Beta:   " . sprintf('%0.25f', $rate/$test) . PHP_EOL);
	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		$result = $parameter->betaParse2($params);
		$end = microtime(true);
		if ($end - $start == 0) {
			$i--;
		}
		$rate += $end-$start;
	}
	if ($out && $result instanceof BrokenSyntaxResult) {
		echo $result->getBrokenSyntax() . "\n";
	}
	echo("Test Beta2:  " . sprintf('%0.25f', $rate/$test) . PHP_EOL);
	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		$result = $parameter->betaParse3($params);
		$end = microtime(true);
		if ($end - $start == 0) {
			$i--;
		}
		$rate += $end-$start;
	}
	if ($out && $result instanceof BrokenSyntaxResult) {
		echo $result->getBrokenSyntax() . "\n";
	}
	echo("Test Beta3:  " . sprintf('%0.25f', $rate/$test) . PHP_EOL);
	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		$result = $parameter->omegaParse($params);
		$end = microtime(true);
		if ($end - $start == 0) {
			$i--;
		}
		$rate += $end-$start;
	}
	if ($out && $result instanceof BrokenSyntaxResult) {
		echo $result->getBrokenSyntax() . "\n";
	}
	echo("Test Omega:  " . sprintf('%0.25f', $rate/$test) . PHP_EOL);
	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		$result = $parameter->omegaParse2($params);
		$end = microtime(true);
		if ($end - $start == 0) {
			$i--;
		}
		$rate += $end-$start;
	}
	if ($out && $result instanceof BrokenSyntaxResult) {
		echo $result->getBrokenSyntax() . "\n";
	}
	echo("Test Omega2: " . sprintf('%0.25f', $rate/$test) . PHP_EOL);

	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		$result = $parameter->deltaParse($params);
		$end = microtime(true);
		if ($end - $start == 0) {
			$i--;
		}
		$rate += $end-$start;
	}
	if ($out && $result instanceof BrokenSyntaxResult) {
		echo $result->getBrokenSyntax() . "\n";
	}
	echo("Test Delta:  " . sprintf('%0.25f', $rate/$test) . PHP_EOL);
	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		$result = $parameter->deltaParse2($params);
		$end = microtime(true);
		if ($end - $start == 0) {
			$i--;
		}
		$rate += $end-$start;
	}
	if ($out && $result instanceof BrokenSyntaxResult) {
		echo $result->getBrokenSyntax() . "\n";
	}
	echo("Test Delta2: " . sprintf('%0.25f', $rate/$test) . PHP_EOL);
	echo ("-----------------------------------------------------------------\n");
}