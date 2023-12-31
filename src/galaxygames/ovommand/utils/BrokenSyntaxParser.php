<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use pocketmine\lang\Translatable;

class BrokenSyntaxParser{
	public const SYNTAX_PRINT_VANILLA = 0b0001;
	public const SYNTAX_PRINT_OVOMMAND = 0b0010;
	public const SYNTAX_TRIMMED = 0b0100;

	public const COMMAND_GENERIC_SYNTAX_KEY = "commands.generic.syntax";
	public const OVO_GENERIC_SYNTAX_MESSAGE = "Syntax error: Unexpected \"{broken_syntax}\": at \"{previous}>>{broken_syntax}<<{after}\"";

	/** @param string[] $nonParsedArgs */
	public static function parseFromBrokenSyntaxResult(BrokenSyntaxResult $result, int $flags = self::SYNTAX_PRINT_OVOMMAND | self::SYNTAX_TRIMMED, array $nonParsedArgs = []) : Translatable|string{
		$fullCMD = "/" . $result->getPreLabel() . " " . $result->getFullSyntax() . Utils::implode($nonParsedArgs);
		if ($result->getCode() === BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS) {
			$translate = [
				"previous" => $fullCMD, "broken_syntax" => "", "after" => "",
			];
			$message = self::OVO_GENERIC_SYNTAX_MESSAGE;
			return self::translate($message, $translate);
		}
		if ($result->getCode() === BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS) {
			$translate = [
				"previous" => "/" . $result->getPreLabel() . " ", "broken_syntax" => $result->getBrokenSyntax(), "after" => Utils::implode($nonParsedArgs),
			];
			$message = self::OVO_GENERIC_SYNTAX_MESSAGE;
			return self::translate($message, $translate);
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
			$message = self::OVO_GENERIC_SYNTAX_MESSAGE;
			$translate = [
				"previous" => $parts[0], "broken_syntax" => $brokenPart, "after" => $parts[1],
			];
			return self::translate($message, $translate);
		}
		return self::parseVanillaSyntaxMessage($parts[0], $brokenPart, $parts[1]);
	}

	public static function parseVanillaSyntaxMessage(string $previous, string $brokenPart, string $after) : Translatable|string{
		return (new Translatable(self::COMMAND_GENERIC_SYNTAX_KEY, [$previous, $brokenPart, $after]));
	}

	/** @param array<string, string> $tags */
	private static function translate(string $msg, array $tags) : string{
		foreach ($tags as $tag => $value) {
			$msg = str_replace('{' . $tag . '}', $value, $msg);
		}
		return $msg;
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
