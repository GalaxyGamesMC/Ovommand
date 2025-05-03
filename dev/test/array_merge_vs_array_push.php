<?php
declare(strict_types=1);

// Simulate subcommand usage generation
$subCommandUsages = 100;  // Simulated usage list elements for a subcommand
$aliasesPerSubCommand = 10; // Number of aliases per subcommand
$subCommandsCount = 50; // Number of subcommands
$iterations = 500; // Iterations for benchmarking

// Generate test data for subcommands and their aliases
$dataToMerge = [];
for ($i = 0; $i < $subCommandsCount; ++$i) {
	$subCommandUsageList = [];
	for ($j = 0; $j < $subCommandUsages; ++$j) {
		$subCommandUsageList[] = uniqid('', true); // Unique simulated usage
	}
	$dataToMerge[$i]['usageList'] = $subCommandUsageList;
	$dataToMerge[$i]['aliases'] = [];
	for ($a = 0; $a < $aliasesPerSubCommand; ++$a) {
		$dataToMerge[$i]['aliases'][] = 'alias_' . uniqid('', true);
	}
}

$results = [];
$memoryUsage = [];

// Method 1: Original approach with nested loops and array_push
for ($i = 0; $i < $iterations; ++$i) {
	$start = microtime(true);
	$usages = [];
	foreach ($dataToMerge as $subCommand) {
		// Add usages by main command name
		array_push($usages, ...array_map(
			static fn(string $usage) => "main " . $usage,
			$subCommand['usageList']
		));

		// Add usages for each alias
		foreach ($subCommand['aliases'] as $alias) {
			array_push($usages, ...array_map(
				static fn(string $usage) => $alias . " " . $usage,
				$subCommand['usageList']
			));
		}
	}
	$time = microtime(true) - $start;

	$name = 'Original (ArrayPush)';
	$results[$name][] = $time;
	$memoryUsage[$name] = memory_get_peak_usage(true);
}

// Method 2: Optimized approach using array_merge and array_map
for ($i = 0; $i < $iterations; ++$i) {
	$start = microtime(true);
	$usages = [];
	foreach ($dataToMerge as $subCommand) {
		$usages = array_merge(
			$usages,
			array_map(static fn(string $usage) => "main " . $usage, $subCommand['usageList']),
			...array_map(
				static fn(string $alias) => array_map(
					static fn(string $usage) => $alias . " " . $usage,
					$subCommand['usageList']
				),
				$subCommand['aliases']
			)
		);
	}
	$time = microtime(true) - $start;

	$name = 'Optimized (ArrayMerge)';
	$results[$name][] = $time;
	$memoryUsage[$name] = memory_get_peak_usage(true);
}

// Output results
echo 'Subcommands count: ' . $subCommandsCount . '; usages per subcommand: ' . $subCommandUsages . '; aliases per subcommand: ' . $aliasesPerSubCommand . '; iterations: ' . $iterations . "\n";

foreach ($results as $test => $times) {
	echo $test . "\n" . implode("\t", [
			'[avg]: ' . (array_sum($times) / count($times)),
			'[min]: ' . min($times),
			'[max]: ' . max($times),
			'[mem_usage]: ' . $memoryUsage[$test],
		]) . "\n";
}

//Subcommands count: 50; usages per subcommand: 100; aliases per subcommand: 10; iterations: 500
//Original (ArrayPush)
//[avg]: 0.0073280591964722	[min]: 0.0058801174163818	[max]: 0.021941900253296	[mem_usage]: 8388608
//Optimized (ArrayMerge)
//[avg]: 0.042733723640442	[min]: 0.03646993637085	[max]: 0.064539194107056	[mem_usage]: 18874368
