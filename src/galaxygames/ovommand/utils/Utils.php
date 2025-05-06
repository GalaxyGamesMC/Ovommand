<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

use galaxygames\ovommand\enum\HardEnum;

class Utils{
	/**
	 * $separator . implode($sub_separator . $separator, $input) || ""
	 * @param string[] $input
	 */
	public static function implode(array $input, string $separator = " ", string $sub_separator = "") : string{
		if (count($input) === 0) {
			return "";
		}
		return $separator . implode($sub_separator . $separator, $input);
	}

	/** @return list<string> */
	public static function uniqueList(array $input) : array{
		return array_values(array_unique($input));
	}

	/**
	 * @param string[] $context
	 * @param array<string, string|string[]> $showAliases
	 * @param array<string, string|string[]> $hiddenAliases
	 */
	public static function hardEnumFromList(string $name, array $context, array $showAliases = [], array $hiddenAliases = [], bool $isProtected = false, bool $isDefault = false) : HardEnum{
		$inputs = [];
		foreach ($context as $value) {
			$inputs[$value] = $value;
		}
		return new HardEnum($name, $inputs, $showAliases, $hiddenAliases, $isProtected, $isDefault);
	}
}
