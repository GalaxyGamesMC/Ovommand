<?php
//declare(strict_types=1);
//
//function test1(array $parameters) : void{
//	$newParameters = [];
//	foreach ($parameters as $parameter) { //special case, ~~~
//		$s = $parameter;
//		$l = strlen($s);
//
//		$currentPos = 0;
//
//		while ($currentPos < $l) {
//			$tildePos = strpos($s, "~", $currentPos + 1);
//			$caretPos = strpos($s, "^", $currentPos + 1);
//			if ($tildePos === false) {
//				$tildePos = $l;
//			}
//			if ($caretPos === false) {
//				$caretPos = $l;
//			}
//			$nextPos = min($tildePos, $caretPos);
//
//			$newParameters[] = substr($s, $currentPos, $nextPos - $currentPos);
//			$currentPos = $nextPos;
//		}
//	}
//	$parameters = $newParameters;
//
//	foreach ($parameters as $parameter) {
//		if (!preg_match("/^([~^]?[+-]?\d*(?:\.\d+)?)$/", $parameter)) {
//			break;
//		}
//	}
//}
//
//function test1U(array $parameters) : void{
//	$newParameters = [];
//	foreach ($parameters as $parameter) { //special case, ~~~
//		$s = $parameter;
//		$l = strlen($s);
//
//		$currentPos = 0;
//
//		while ($currentPos < $l) {
//			$tildePos = strpos($s, "~", $currentPos + 1);
//			$caretPos = strpos($s, "^", $currentPos + 1);
//			if ($tildePos === false) {
//				$tildePos = $l;
//			}
//			if ($caretPos === false) {
//				$caretPos = $l;
//			}
//			$nextPos = min($tildePos, $caretPos);
//
//			$newParameters[] = substr($s, $currentPos, $nextPos - $currentPos);
//			$currentPos = $nextPos;
//		}
//	}
//	$parameters = $newParameters;
//
//	foreach ($parameters as $parameter) {
//		if (!preg_match("/^([~^]?[+-]?\d*(?:\.\d+)?)$/U", $parameter)) {
//			break;
//		}
//	}
//}
//
//function test1A(array $parameters) : void{
//	$newParameters = [];
//	foreach ($parameters as $parameter) { //special case, ~~~
//		$s = $parameter;
//		$l = strlen($s);
//
//		$currentPos = 0;
//
//		while ($currentPos < $l) {
//			$tildePos = strpos($s, "~", $currentPos + 1);
//			$caretPos = strpos($s, "^", $currentPos + 1);
//			if ($tildePos === false) {
//				$tildePos = $l;
//			}
//			if ($caretPos === false) {
//				$caretPos = $l;
//			}
//			$nextPos = min($tildePos, $caretPos);
//
//			$newParameters[] = substr($s, $currentPos, $nextPos - $currentPos);
//			$currentPos = $nextPos;
//		}
//	}
//	$parameters = $newParameters;
//
//	foreach ($parameters as $parameter) {
//		if (!preg_match("/^[^ ][~^]?[+-]?\d+(?:\.\d+)?$/", $parameter)) {
//			break;
//		}
//	}
//}
//
//function test1B(array $parameters) : void{
//	$newParameters = [];
//	foreach ($parameters as $parameter) { //special case, ~~~
//		$s = $parameter;
//		$l = strlen($s);
//
//		$currentPos = 0;
//
//		while ($currentPos < $l) {
//			$tildePos = strpos($s, "~", $currentPos + 1);
//			$caretPos = strpos($s, "^", $currentPos + 1);
//			if ($tildePos === false) {
//				$tildePos = $l;
//			}
//			if ($caretPos === false) {
//				$caretPos = $l;
//			}
//			$nextPos = min($tildePos, $caretPos);
//
//			$newParameters[] = substr($s, $currentPos, $nextPos - $currentPos);
//			$currentPos = $nextPos;
//		}
//	}
//	$parameters = $newParameters;
//
//	foreach ($parameters as $parameter) {
//		if (!@preg_match("/^[^\0]+(?:[~^]?[+-]?\d+\.\d+)?|[~^]?[+-]?\d+$/", $parameter)) {
//			break;
//		}
//	}
//}
//function test1S(array $parameters) : void{
//	$newParameters = [];
//	foreach ($parameters as $parameter) { //special case, ~~~
//		$s = $parameter;
//		$l = strlen($s);
//
//		$currentPos = 0;
//
//		while ($currentPos < $l) {
//			$tildePos = strpos($s, "~", $currentPos + 1);
//			$caretPos = strpos($s, "^", $currentPos + 1);
//			if ($tildePos === false) {
//				$tildePos = $l;
//			}
//			if ($caretPos === false) {
//				$caretPos = $l;
//			}
//			$nextPos = min($tildePos, $caretPos);
//
//			$newParameters[] = substr($s, $currentPos, $nextPos - $currentPos);
//			$currentPos = $nextPos;
//		}
//	}
//	$parameters = $newParameters;
//
//	foreach ($parameters as $parameter) {
//		if (!@preg_match("/([~^]?[+-]?(\S\d*(?:\.\d+)?))/", $parameter)) {
//			break;
//		}
//	}
//}
//
//function test2(array $parameters) : void{
//	$parameter = implode(" ", $parameters);
//	if (!preg_match("/^([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?)$/", $parameter, $matches)) {
//		var_dump($parameter);
//	}
//}
//function test2U(array $parameters) : void{
//	$parameter = implode(" ", $parameters);
//	if (!preg_match("/^([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?)$/U", $parameter, $matches)) {
//		var_dump($parameter);
//	}
//}
//
//function test3(array $parameters) : void{
//	$parameter = implode(" ", $parameters);
////	if (!preg_match("/^([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?)$/", $parameter, $matches)) {
//	if (!preg_match("/^([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?)$/", $parameter, $matches)) {
//		var_dump($parameter);
//	}
//}
//function test3U(array $parameters) : void{
//	$parameter = implode(" ", $parameters);
//	//	if (!preg_match("/^([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?) *([~^]?[+-]?\d*?\.?\d+?)$/U", $parameter, $matches)) {
//	if (!preg_match("/^([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?)$/U", $parameter, $matches)) {
//		var_dump($parameter);
//	}
//}
//
//function test4(array $parameters) : void{
//	$parameter = implode(" ", $parameters);
//	if (!preg_match("/^([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?)$/", $parameter, $matches)) {
//		var_dump($parameter);
//	}
//}
//function test4U(array $parameters) : void{
//	$parameter = implode(" ", $parameters);
//	if (!preg_match("/^([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?)$/U", $parameter, $matches)) {
//		var_dump($parameter);
//	}
//}
//
//function test5(array $parameters) : void{
//	$parameter = implode(" ", $parameters);
//	if (!preg_match("/^([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?)$/", $parameter, $matches)) {
//	}
//}
//function test5U(array $parameters) : void{
//	$parameter = implode(" ", $parameters);
//	if (!preg_match("/^([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?) *([~^]?[+-]?(\d+(?:\.\d+)?)?)$/U", $parameter, $matches)) {
//	}
//}
//
//function test6(array $parameters) : void{ // good
//	$parameter = implode(" ", $parameters);
//	if (!preg_match("/([~^]?[+-]?(?:\d*\.\d+|\d+)?)\s?([~^]?[+-]?(?:\d*\.\d+|\d+)?)\s?([~^]?[+-]?(?:\d*\.\d+|\d+)?)(\S+)?/", $parameter, $matches)) {
//		return;
//	}
//
////	var_dump($matches);
//}
//
//
//$parameters = [
//	["~131318763183~-131873173", "~11232213123213123123213213763172791281631287162317863781.1321368763287812"],
//	["~~~"],
//	["~~", "~"],
//	["^^^a"],
//	["~131318763183~-131873173", "~1123221312321312312321321376317a2791281631287162317863781.1321368763287812"],
//	["~13131", "~+1123221312321312312", "-13132313133"],
//	["13131", "+1123221312321312312", "-13132313133"],
////	[""],
////	[" ", "  "],
//];
//
////$test = 50000;
//$test = 1;
//
//$tz = 'GMT+7';
//$timestamp = time();
//try {
//	$dt = new DateTime("now", new DateTimeZone($tz));
//	$dt->setTimestamp($timestamp);
//	echo "Test with $test entries, " . count($parameters) . " inputs, " . $dt->format("h:i d/m/Y T") . "\n\n";
//} catch (Exception $e) {
//	echo $e->getMessage();
//}
//
//foreach ($parameters as $parameter) {
//	echo "\"" . implode(" ", $parameter) . "\"" . "\n";
//	$rate = 0;
//	for ($i = 1; $i <= $test; ++$i) {
//		$start = microtime(true);
//		test1($parameter);
//		$end = microtime(true);
//		$rate += $end - $start;
//	}
//	echo("Test1 : " . sprintf('%0.25f', $rate/$test) . "\n");
//
////	$rate = 0;
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test1A($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test1A: " . sprintf('%0.25f', $rate/$test) . "\n");$rate = 0;
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test1B($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test1B: " . sprintf('%0.25f', $rate/$test) . "\n");
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test1S($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test1S: " . sprintf('%0.25f', $rate/$test) . "\n");
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test1U($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test1U: " . sprintf('%0.25f', $rate/$test) . "\n");
////
////	$rate = 0;
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test2($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test2 : " . sprintf('%0.25f', $rate/$test) . "\n");
////
////	$rate = 0;
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test2U($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test2U: " . sprintf('%0.25f', $rate/$test) . "\n");
////
////	$rate = 0;
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test3($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test3 : " . sprintf('%0.25f', $rate/$test) . "\n");
////
////	$rate = 0;
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test3U($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test3U: " . sprintf('%0.25f', $rate/$test) . "\n");
////
////	$rate = 0;
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test4($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test4 : " . sprintf('%0.25f', $rate/$test) . "\n");
////
////	$rate = 0;
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test4U($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test4U: " . sprintf('%0.25f', $rate/$test) . "\n");
////	$rate = 0;
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test5($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test5 : " . sprintf('%0.25f', $rate/$test) . "\n");
////
////	$rate = 0;
////	for ($i = 1; $i <= $test; ++$i) {
////		$start = microtime(true);
////		test5U($parameter);
////		$end = microtime(true);
////		$rate += $end - $start;
////	}
////	echo("Test5U: " . sprintf('%0.25f', $rate/$test) . "\n");
//	$rate = 0;
//	for ($i = 1; $i <= $test; ++$i) {
//		$start = microtime(true);
//		test6($parameter);
//		$end = microtime(true);
//		$rate += $end - $start;
//	}
//	echo("Test6 : " . sprintf('%0.25f', $rate/$test) . "\n");
//
//	echo "-----------------------------------------------------------------\n";
//}
//
//
///*
//
//Test with 500 entries, 2 inputs, 12:35 08/10/2023 GMT+0700
//
//~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812
//Test1 : 0.0000018925666809082030589
//Test1U: 0.0000018916130065917969140
//Test2 : 0.0000082430839538574214142
//Test2U: 0.0000019340515136718750237
//-----------------------------------------------------------------
//~~~
//Test1 : 0.0000015988349914550782216
//Test1U: 0.0000015769004821777343462
//Test2 : 0.0000007305145263671875136
//Test2U: 0.0000006017684936523437551
//-----------------------------------------------------------------
//
//Test with 500 entries, 2 inputs, 12:36 08/10/2023 GMT+0700
//
//~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812
//Test1 : 0.0000017766952514648437398
//Test1U: 0.0000022535324096679687398
//Test2 : 0.0000064907073974609375474
//Test2U: 0.0000012307167053222656064
//-----------------------------------------------------------------
//~~~
//Test1 : 0.0000013580322265624999356
//Test1U: 0.0000013213157653808593310
//Test2 : 0.0000004243850708007812564
//Test2U: 0.0000005149841308593750339
//-----------------------------------------------------------------
//
//Test with 5000000 entries, 2 inputs, 12:36 08/10/2023 GMT+0700
//
//~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812
//Test1 : 0.0000011176638126373291108
//Test1U: 0.0000012560600280761719604
//Test2 : 0.0000034558418273925779773
//Test2U: 0.0000006915743827819824103
//-----------------------------------------------------------------
//~~~
//Test1 : 0.0000009099048614501952778
//Test1U: 0.0000009042129516601562298
//Test2 : 0.0000002902111530303955155
//Test2U: 0.0000003368832588195800760
//-----------------------------------------------------------------
//
//Test with 5000 entries, 4 inputs, 12:38 08/10/2023 GMT+0700
//
//~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812
//Test1 : 0.0000022773265838623047038
//Test1U: 0.0000018608570098876952447
//Test2 : 0.0000048291206359863279990
//Test2U: 0.0000008414268493652343494
//-----------------------------------------------------------------
//~~~
//Test1 : 0.0000012257099151611328159
//Test1U: 0.0000009349346160888672397
//Test2 : 0.0000002693176269531249851
//Test2U: 0.0000003006935119628906485
//-----------------------------------------------------------------
//~~ ~
//Test1 : 0.0000008916378021240233877
//Test1U: 0.0000009567260742187500874
//Test2 : 0.0000002995014190673828085
//Test2U: 0.0000003350734710693359333
//-----------------------------------------------------------------
//^^^
//Test1 : 0.0000008532524108886719235
//Test1U: 0.0000009200572967529296841
//Test2 : 0.0000003035545349121094011
//Test2U: 0.0000003161907196044921983
//-----------------------------------------------------------------
//
//
//Test with 5000 entries, 5 inputs, 12:39 08/10/2023 GMT+0700
//
//~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812
//Test1 : 0.0000015785694122314454175
//Test1U: 0.0000022873401641845702847
//Test2 : 0.0000060431480407714847978
//Test2U: 0.0000009408950805664062810
//-----------------------------------------------------------------
//~~~
//Test1 : 0.0000013170242309570312551
//Test1U: 0.0000011833190917968749075
//Test2 : 0.0000003714084625244140800
//Test2U: 0.0000004338264465332031503
//-----------------------------------------------------------------
//~~ ~
//Test1 : 0.0000011878490447998046255
//Test1U: 0.0000012138366699218749346
//Test2 : 0.0000004238128662109375164
//Test2U: 0.0000005070686340332030777
//-----------------------------------------------------------------
//^^^
//Test1 : 0.0000012024879455566405389
//Test1U: 0.0000016689300537109375000
//Test2 : 0.0000003521919250488281413
//Test2U: 0.0000003459930419921875108
//-----------------------------------------------------------------
//~131318763183~-131873173 ~1123221312321312312321321376317a2791281631287162317863781.1321368763287812
//Test1 : 0.0000011959075927734375034
//Test1U: 0.0000012720584869384765435
//Test2 : 0.0000045701503753662112750
//Test2U: 0.0000020121097564697263538
//-----------------------------------------------------------------
//This is noticeable, parsing broken syntax
//*/
///*
//Test with 50000 entries, 4 inputs, 02:15 08/10/2023 GMT+0700
//
//"~131318763183~-131873173 ~11232213123213123123213213763172791281631287162317863781.1321368763287812"
//Test1 : 0.0000012811279296875001045
//Test1U: 0.0000012058210372924804074
//Test2 : 0.0000036989641189575196460
//Test2U: 0.0000007299995422363281529 x
//Test3 : 0.0000039481115341186524416
//Test3U: 0.0000011243486404418945701
//Test4 : 0.0000007435607910156250378 x
//Test4U: 0.0000065884923934936520085
//Test5 : 0.0000005068397521972656665 x
//Test5U: 0.0000049139499664306644406
//-----------------------------------------------------------------
//"~131318763183~-131873173 ~1123221312321312312321321376317a2791281631287162317863781.1321368763287812"
//Test1 : 0.0000011592960357666016382 x
//Test1U: 0.0000012606048583984374084 x
//Test2 : 0.0000045936489105224610587
//Test2U: 0.0000019852924346923826774 x
//Test3 : 0.0000047993087768554689850
//Test3U: 0.0000018919563293457030838 x
//Test4 : 0.0000024954938888549805872
//Test4U: 0.0000041650962829589844512
//Test5 : 0.0000024163818359375000117
//Test5U: 0.0000045393037796020508420
//-----------------------------------------------------------------
//"~13131 ~+1123221312321312312 -13132313133"
//Test1 : 0.0000008874511718750000513
//Test1U: 0.0000009639358520507813481
//Test2 : 0.0000018684148788452149495
//Test2U: 0.0000006901979446411132352 x
//Test3 : 0.0000019666719436645508440
//Test3U: 0.0000006048870086669921879 x
//Test4 : 0.0000004927730560302734400 x
//Test4U: 0.0000027638864517211913787
//Test5 : 0.0000004858398437500000038 x
//Test5U: 0.0000025120973587036133287
//-----------------------------------------------------------------
//"13131 +1123221312321312312 -13132313133"
//Test1 : 0.0000009563636779785156676
//Test1U: 0.0000011787176132202148875
//Test2 : 0.0000017552232742309569613
//Test2U: 0.0000004440402984619140853 x
//Test3 : 0.0000021435976028442382555
//Test3U: 0.0000004189014434814452875 x
//Test4 : 0.0000004186964035034179769 x
//Test4U: 0.0000021883296966552735992
//Test5 : 0.0000004560184478759765472 x
//Test5U: 0.0000023733997344970703781
//
// */