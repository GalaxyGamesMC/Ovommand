<?php
declare(strict_types=1);

function test1(array $parameters) : void{
	$newParameters = [];
	foreach ($parameters as $parameter) { //special case, ~~~
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

			$newParameters[] = substr($s, $currentPos, $nextPos - $currentPos);
			$currentPos = $nextPos;
		}
	}
	$parameters = $newParameters;

	foreach ($parameters as $parameter) {
		if (!preg_match("/^([~^]?[+-]?\d*(?:\.\d+)?)$/", $parameter)) {
			break;
		}
	}
}
function test1U(array $parameters) : void{
	$newParameters = [];
	foreach ($parameters as $parameter) { //special case, ~~~
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

			$newParameters[] = substr($s, $currentPos, $nextPos - $currentPos);
			$currentPos = $nextPos;
		}
	}
	$parameters = $newParameters;

	foreach ($parameters as $parameter) {
		if (!preg_match("/^([~^]?[+-]?\d*(?:\.\d+)?)$/U", $parameter)) {
			break;
		}
	}
}
function test2(array $parameters) : void{
	$parameter = implode(" ", $parameters);
	if (!preg_match("/^([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?)$/", $parameter, $matches)) {
//		var_dump($parameter);
	}
}
function test2U(array $parameters) : void{
	$parameter = implode(" ", $parameters);
	if (!preg_match("/^([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?)$/U", $parameter, $matches)) {
//		var_dump($parameter);
	}
}

function test3(array $parameters) : void{
	$parameter = implode(" ", $parameters);
//	if (!preg_match("/^([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?)$/", $parameter, $matches)) {
		if (!preg_match("/^([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?)$/", $parameter, $matches)) {
//		var_dump($parameter);
	}
}
function test3U(array $parameters) : void{
	$parameter = implode(" ", $parameters);
	//	if (!preg_match("/^([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?)$/U", $parameter, $matches)) {
	if (!preg_match("/^([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?)$/U", $parameter, $matches)) {
//		var_dump($parameter);
	}
}

function test4(array $parameters) : void{
	$parameter = implode(" ", $parameters);
	//	if (!preg_match("/^([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?)$/", $parameter, $matches)) {
	if (!preg_match("/^([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?)$/", $parameter, $matches)) {
//		var_dump($parameter);
	}
}
function test4U(array $parameters) : void{
	$parameter = implode(" ", $parameters);
	//	if (!preg_match("/^([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?)$/U", $parameter, $matches)) {
	if (!preg_match("/^([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?)$/U", $parameter, $matches)) {
//		var_dump($parameter);
	}
}

$parameters = [
	["~131318763183~-131873173", "~11232213123213123123213213763172791281631287162317863781.1321368763287812"],
	["~~~"],
	["~~", "~"],
	["^^^"],
	["~131318763183~-131873173", "~1123221312321312312321321376317a2791281631287162317863781.1321368763287812"],
	["~13131", "~+1123221312321312312", "-13132313133"],
	[""],
	[" ", "  "],
];

$test = 50000;

$tz = 'GMT+7';
$timestamp = time();
try {
	$dt = new DateTime("now", new DateTimeZone($tz));
	$dt->setTimestamp($timestamp);
	echo "Test with $test entries, " . count($parameters) . " inputs, " . $dt->format("h:i d/m/Y T") . "\n\n";
} catch (Exception $e) {
	echo $e->getMessage();
}

foreach ($parameters as $parameter) {
	echo "\"" . implode(" ", $parameter) . "\"" . "\n";
	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		test1($parameter);
		$end = microtime(true);
		$rate += $end - $start;
	}
	echo("Test1 : " . sprintf('%0.25f', $rate/$test) . "\n");

	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		test1U($parameter);
		$end = microtime(true);
		$rate += $end - $start;
	}
	echo("Test1U: " . sprintf('%0.25f', $rate/$test) . "\n");

	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		test2($parameter);
		$end = microtime(true);
		$rate += $end - $start;
	}
	echo("Test2 : " . sprintf('%0.25f', $rate/$test) . "\n");

	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		test2U($parameter);
		$end = microtime(true);
		$rate += $end - $start;
	}
	echo("Test2U: " . sprintf('%0.25f', $rate/$test) . "\n");

	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		test3($parameter);
		$end = microtime(true);
		$rate += $end - $start;
	}
	echo("Test3 : " . sprintf('%0.25f', $rate/$test) . "\n");

	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		test3U($parameter);
		$end = microtime(true);
		$rate += $end - $start;
	}
	echo("Test3U: " . sprintf('%0.25f', $rate/$test) . "\n");

	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		test4($parameter);
		$end = microtime(true);
		$rate += $end - $start;
	}
	echo("Test4 : " . sprintf('%0.25f', $rate/$test) . "\n");

	$rate = 0;
	for ($i = 1; $i <= $test; ++$i) {
		$start = microtime(true);
		test4U($parameter);
		$end = microtime(true);
		$rate += $end - $start;
	}
	echo("Test4U: " . sprintf('%0.25f', $rate/$test) . "\n");

	echo "-----------------------------------------------------------------\n";
}

/*

Test with 500 entries, 2 inputs, 12:35 08/10/2023 GMT+0700

~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812
Test1 : 0.0000018925666809082030589
Test1U: 0.0000018916130065917969140
Test2 : 0.0000082430839538574214142
Test2U: 0.0000019340515136718750237
-----------------------------------------------------------------
~~~
Test1 : 0.0000015988349914550782216
Test1U: 0.0000015769004821777343462
Test2 : 0.0000007305145263671875136
Test2U: 0.0000006017684936523437551
-----------------------------------------------------------------

Test with 500 entries, 2 inputs, 12:36 08/10/2023 GMT+0700

~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812
Test1 : 0.0000017766952514648437398
Test1U: 0.0000022535324096679687398
Test2 : 0.0000064907073974609375474
Test2U: 0.0000012307167053222656064
-----------------------------------------------------------------
~~~
Test1 : 0.0000013580322265624999356
Test1U: 0.0000013213157653808593310
Test2 : 0.0000004243850708007812564
Test2U: 0.0000005149841308593750339
-----------------------------------------------------------------

Test with 5000000 entries, 2 inputs, 12:36 08/10/2023 GMT+0700

~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812
Test1 : 0.0000011176638126373291108
Test1U: 0.0000012560600280761719604
Test2 : 0.0000034558418273925779773
Test2U: 0.0000006915743827819824103
-----------------------------------------------------------------
~~~
Test1 : 0.0000009099048614501952778
Test1U: 0.0000009042129516601562298
Test2 : 0.0000002902111530303955155
Test2U: 0.0000003368832588195800760
-----------------------------------------------------------------

Test with 5000 entries, 4 inputs, 12:38 08/10/2023 GMT+0700

~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812
Test1 : 0.0000022773265838623047038
Test1U: 0.0000018608570098876952447
Test2 : 0.0000048291206359863279990
Test2U: 0.0000008414268493652343494
-----------------------------------------------------------------
~~~
Test1 : 0.0000012257099151611328159
Test1U: 0.0000009349346160888672397
Test2 : 0.0000002693176269531249851
Test2U: 0.0000003006935119628906485
-----------------------------------------------------------------
~~ ~
Test1 : 0.0000008916378021240233877
Test1U: 0.0000009567260742187500874
Test2 : 0.0000002995014190673828085
Test2U: 0.0000003350734710693359333
-----------------------------------------------------------------
^^^
Test1 : 0.0000008532524108886719235
Test1U: 0.0000009200572967529296841
Test2 : 0.0000003035545349121094011
Test2U: 0.0000003161907196044921983
-----------------------------------------------------------------


Test with 5000 entries, 5 inputs, 12:39 08/10/2023 GMT+0700

~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812
Test1 : 0.0000015785694122314454175
Test1U: 0.0000022873401641845702847
Test2 : 0.0000060431480407714847978
Test2U: 0.0000009408950805664062810
-----------------------------------------------------------------
~~~
Test1 : 0.0000013170242309570312551
Test1U: 0.0000011833190917968749075
Test2 : 0.0000003714084625244140800
Test2U: 0.0000004338264465332031503
-----------------------------------------------------------------
~~ ~
Test1 : 0.0000011878490447998046255
Test1U: 0.0000012138366699218749346
Test2 : 0.0000004238128662109375164
Test2U: 0.0000005070686340332030777
-----------------------------------------------------------------
^^^
Test1 : 0.0000012024879455566405389
Test1U: 0.0000016689300537109375000
Test2 : 0.0000003521919250488281413
Test2U: 0.0000003459930419921875108
-----------------------------------------------------------------
~131318763183~-131873173 ~1123221312321312312321321376317a2791281631287162317863781.1321368763287812
Test1 : 0.0000011959075927734375034
Test1U: 0.0000012720584869384765435
Test2 : 0.0000045701503753662112750
Test2U: 0.0000020121097564697263538
-----------------------------------------------------------------
This is noticeable, parsing broken syntax
*/