<?php
declare(strict_types=1);

use pocketmine\utils\AssumptionFailedError;

function parseQuoteAware(string $commandLine) : array{
	$args = [];
	preg_match_all('/"((?:\\\\.|[^\\\\"])*)"|(\S+)/u', $commandLine, $matches);
	foreach($matches[0] as $k => $_){
		for($i = 1; $i <= 2; ++$i){
			if($matches[$i][$k] !== ""){
				$match = $matches[$i][$k];
				$args[(int) $k] = preg_replace('/\\\\([\\\\"])/u', '$1', $match) ?? throw new AssumptionFailedError(preg_last_error_msg());
				break;
			}
		}
	}

	return $args;
}

var_dump($args = parseQuoteAware("§aa"));
var_dump($args = parseQuoteAware("§"));
var_dump($args = parseQuoteAware("§∧∨ì|—–₱ʑ⇋◿∆🗣️😶aa"));
var_dump($args = parseQuoteAware(substr("\xc2\xa7 aaaaaaaaaaa", 0)));