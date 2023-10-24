<?php
declare(strict_types=1);

require_once "D:\pmmp\Ovommand\\vendor\autoload.php";

use galaxygames\ovommand\parameter\PositionParameter;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;

$parameter = new PositionParameter("t");

$test = 100;
$test = 100000;
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
	["13131", "+1123221312321312312", "-13132313133", "~12312"],
	["adadad~adadadada~adada adsadadad adad+133231😿😿😿😭✨💀😊~asdad adadaadsada"],
	["~adadadada~adada adsadadad adad+133231😿😿😿😭✨💀😊~asdad adadaadsada"],
	["~~adada adsadadad adad+133231😿😿😿😭✨💀😊~asdad adadaadsada"],
	["~~+133231😿😿😿😭✨💀😊~asdad adadaadsada"],
	["~~+133231~asdad adadaadsada"],
	["~~+133231~~13132"],
	["~~+133231               ~~13132"],
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
		$time = -hrtime(true);
		$result = $parameter->parse($params);
		$time += hrtime(true);
		$rate += $time;
	}
	if ($out && $result instanceof BrokenSyntaxResult) {
		echo "\033[31m" . var_export($result->getBrokenSyntax(), true) . "\033[0m\n";
	}
	echo("Test1: " . sprintf('%0.4fns', $rate/$test) . "\n");
	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$time = -hrtime(true);
		$result = $parameter->parse($params);
		$time += hrtime(true);
		$rate += $time;
	}
	if ($out && $result instanceof BrokenSyntaxResult) {
		echo "\033[31m" . var_export($result->getBrokenSyntax(), true) . "\033[0m\n";
	}
	echo("Test2: " . sprintf('%0.4fns', $rate/$test) . "\n");
	echo("------------------=---------------\n");
}