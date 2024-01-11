<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

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
}
