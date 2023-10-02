<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

use pocketmine\utils\TextFormat;

class UnexpectedMessage{
	public static function create(string $raw, string $unexpected) : string{
		return TextFormat::RED . "Syntax error: Unexpected \"$unexpected\": at \"" . str_replace($unexpected, ">>$unexpected<<", $raw) . "\"";
	}
}
