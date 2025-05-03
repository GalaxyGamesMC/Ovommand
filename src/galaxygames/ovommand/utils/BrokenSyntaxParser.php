<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use pocketmine\lang\Translatable;

class BrokenSyntaxParser{
	public const SYNTAX_PRINT_VANILLA = 0b0001;
	public const SYNTAX_PRINT_OVOMMAND = 0b0010;
	public const SYNTAX_TRIMMED = 0b0100;
	
	public const MESSAGE_TAG_PREVIOUS = "previous";
	public const MESSAGE_TAG_BROKEN_SYNTAX = "broken_syntax";
	public const MESSAGE_TAG_AFTER = "after";

	/** @param string[] $nonParsedArgs */
	public static function parseFromBrokenSyntaxResult(BrokenSyntaxResult $result, int $flags = self::SYNTAX_PRINT_OVOMMAND | self::SYNTAX_TRIMMED, array $nonParsedArgs = []) : Translatable|string{
		$fullCMD = "/" . $result->getPreLabel() . " " . $result->getFullSyntax() . Utils::implode($nonParsedArgs);
		if ($result->getCode() === BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS) {
			return MessageParser::GENERIC_SYNTAX_MESSAGE_OVO->translate([
				self::MESSAGE_TAG_PREVIOUS => $fullCMD, self::MESSAGE_TAG_BROKEN_SYNTAX => "", self::MESSAGE_TAG_AFTER => ""
			]);
		}
		if ($result->getCode() === BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS) {
			return MessageParser::GENERIC_SYNTAX_MESSAGE_OVO->translate([
				self::MESSAGE_TAG_PREVIOUS => "/" . $result->getPreLabel() . " ", self::MESSAGE_TAG_BROKEN_SYNTAX => $result->getBrokenSyntax(), self::MESSAGE_TAG_AFTER => Utils::implode($nonParsedArgs)
			]);
		}
		$brokenPart = $result->getBrokenSyntax();
		$parts = self::getSyntaxBetweenBrokenPart($fullCMD, $brokenPart);
		if ($flags & self::SYNTAX_TRIMMED) {
			$l1 = strlen($parts[0]);
			$l2 = strlen($parts[1]);
			if ($l1 > 9) {
				$parts[0] = substr($parts[0], -9);
			}
			if ($l2 > 9) {
				$parts[1] = substr($parts[1], 0, 9);
			}
		}
		if ($flags & self::SYNTAX_PRINT_OVOMMAND) {
			if ($flags & self::SYNTAX_PRINT_VANILLA) {
				throw new \InvalidArgumentException(MessageParser::EXCEPTION_BROKEN_SYNTAX_PARSER_COLLIDED_FLAG->value);
			}
			return MessageParser::GENERIC_SYNTAX_MESSAGE_OVO->translate([
				self::MESSAGE_TAG_PREVIOUS => $parts[0], self::MESSAGE_TAG_BROKEN_SYNTAX => $brokenPart, self::MESSAGE_TAG_AFTER => $parts[1]
			]);
		}
		return self::parseVanillaSyntaxMessage($parts[0], $brokenPart, $parts[1]);
	}

	public static function parseVanillaSyntaxMessage(string $previous, string $brokenPart, string $after) : Translatable|string{
		return (new Translatable(MessageParser::GENERIC_SYNTAX_MESSAGE_VANILLA->value, [$previous, $brokenPart, $after]));
	}

	/** @return string[] */
	public static function getSyntaxBetweenBrokenPart(string $syntax, string $brokenPart) : array{
		$brokenPartPos = strpos($syntax, $brokenPart);
		if ($brokenPartPos === false) {
			$brokenPartPos = strlen($syntax);
		}

		return [
			substr($syntax, 0, $brokenPartPos), substr($syntax, $brokenPartPos + strlen($brokenPart))
		];
	}
}
