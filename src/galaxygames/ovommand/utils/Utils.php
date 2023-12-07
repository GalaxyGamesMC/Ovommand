<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

class Utils{
	/**
	 * Default separator is " "
	 * @param string[] $input
	 */
	public static function implode(array $input, string $separator = " ") : string{
		if (count($input) === 0) {
			return "";
		}
		return $separator . implode($separator, $input);
	}
}
