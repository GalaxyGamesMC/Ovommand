<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

class Utils{
	/** @param string[] $input */
	public static function implode(array $input) : string{
		if (empty($input)) {
			return "";
		}
		return " " . implode(" ", $input);
	}
}
