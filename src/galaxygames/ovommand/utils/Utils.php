<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

class Utils{
	/** @param string[] $input */
	public static function implode(array $input, string $separator = " ") : string{
		if (empty($input)) {
			return "";
		}
		return $separator . implode($separator, $input);
	}
}
