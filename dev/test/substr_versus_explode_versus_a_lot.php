<?php
function getSyntaxBetweenBrokenPart1(string $syntax, string $brokenPart) : array{
	$brokenPartPos = strpos($syntax, $brokenPart);
	if ($brokenPartPos === false) {
		$brokenPartPos = strlen($syntax);
	}

	return [
		substr($syntax, 0, $brokenPartPos),
		substr($syntax, $brokenPartPos + strlen($brokenPart))
	];
}

function getSyntaxBetweenBrokenPart2(string $syntax, string $brokenPart) : array {
	if (!str_contains($syntax, $brokenPart)) {
		return [$syntax, ""];
	}

	try {
		$parts = explode($brokenPart, $syntax, 2);
		return [$parts[0], $parts[1] ?? ""];
	} catch(\ValueError $e) {
		return [$syntax, ""];
	}

}

function getSyntaxBetweenBrokenPart3(string $syntax, string $brokenPart) : array {
	try {
		$parts = explode($brokenPart, $syntax, 2);
		return [$parts[0], $parts[1] ?? ""];
	} catch(\ValueError $e) {
		return [$syntax, ""];
	}
}

function getSyntaxBetweenBrokenPart4(string $syntax, string $brokenPart) : array {
	if ($brokenPart === "" || !str_contains($syntax, $brokenPart)) {
		return [$syntax, ""];
	}
	$parts = explode($brokenPart, $syntax, 2);
	return [$parts[0], $parts[1] ?? ""];
}

function getSyntaxBetweenBrokenPart5(string $syntax, string $brokenPart) : array {
	if ($brokenPart === "") {
		return [$syntax, ""];
	}
	$parts = explode($brokenPart, $syntax, 2);
	return [$parts[0], $parts[1] ?? ""];
}

function test($syntax, $broken) : void {
	printf("Test: \"$syntax\" >> \"$broken\"\n");
	$etime = microtime(true);
	for ($i = 0; $i < 1000; ++$i) {
		getSyntaxBetweenBrokenPart1($syntax, $broken);
		getSyntaxBetweenBrokenPart1($syntax, $broken);
	}
	$etime = microtime(true) - $etime;
	printf("Time 1: %.2f ns\n", $etime * 1E6);
	usleep(100);

	$etime = microtime(true);
	for ($i = 0; $i < 1000; ++$i) {
		getSyntaxBetweenBrokenPart2($syntax, $broken);
		getSyntaxBetweenBrokenPart2($syntax, $broken);
	}
	$etime = microtime(true) - $etime;
	printf("Time 2: %.2f ns\n", $etime * 1E6);
	usleep(100);

	$etime = microtime(true);
	for ($i = 0; $i < 1000; ++$i) {
		getSyntaxBetweenBrokenPart3($syntax, $broken);
		getSyntaxBetweenBrokenPart3($syntax, $broken);
	}
	$etime = microtime(true) - $etime;
	printf("Time 3: %.2f ns\n", $etime * 1E6);
	usleep(100);

	$etime = microtime(true);
	for ($i = 0; $i < 1000; ++$i) {
		getSyntaxBetweenBrokenPart4($syntax, $broken);
		getSyntaxBetweenBrokenPart4($syntax, $broken);
	}
	$etime = microtime(true) - $etime;
	printf("Time 4: %.2f ns\n", $etime * 1E6);
	usleep(100);

	$etime = microtime(true);
	for ($i = 0; $i < 1000; ++$i) {
		getSyntaxBetweenBrokenPart5($syntax, $broken);
		getSyntaxBetweenBrokenPart5($syntax, $broken);
	}
	$etime = microtime(true) - $etime;
	printf("Time 5: %.2f ns\n", $etime * 1E6);
	usleep(100);
	printf("---------------\n\n");
}

$testCases = [
	["hello world o wa", "o w"],           // Normal case
	["hello world", "xyz"],                // Not found case
	["test", ""],                          // Empty search
	["aaa bbb aaa bbb", "aaa"],           // Multiple occurrences
	[str_repeat("a", 1000) . "xyz", "xyz"], // Long string, pattern at end
	["xyz" . str_repeat("a", 1000), "xyz"], // Long string, pattern at start
	[str_repeat("a", 1000), "b"],          // Long string, not found
];

foreach ($testCases as $test) {
	test($test[0], $test[1]);
}