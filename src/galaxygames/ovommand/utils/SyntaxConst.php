<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use pocketmine\lang\Translatable;

class SyntaxConst{
	public const SYNTAX_PRINT_VANILLA = 0b0001;
	public const SYNTAX_PRINT_OVOMMAND = 0b0010;
	public const SYNTAX_TRIMMED = 0b0100;

	public const COMMAND_GENERIC_SYNTAX_KEY = "commands.generic.syntax";
	public const OVO_GENERIC_SYNTAX_MESSAGE = "Syntax error: Unexpected \"{broken_syntax}\": at \"{previous}>>{broken_syntax}<<{after}\"";

	/**
	 * @param string[] $nonParsedArgs
	 */
	public static function parseFromBrokenSyntaxResult(BrokenSyntaxResult $result, int $flags = self::SYNTAX_PRINT_VANILLA | self::SYNTAX_TRIMMED, array $nonParsedArgs = []) : string{
		$fullCMD = "/" . $result->getPreLabel() . " " . $result->getFullSyntax() . " " . implode(" ", $nonParsedArgs);
		$brokenPart = $result->getBrokenSyntax();
		$parts = self::getSyntaxBetweenBrokenPart($fullCMD, $brokenPart);
		if ($flags & self::SYNTAX_TRIMMED) {
			$parts[0] = self::vanillaShift($parts[0]);
			$parts[1] = self::vanillaShift($parts[1]);
		}
		if ($flags & self::SYNTAX_PRINT_OVOMMAND) {
			if ($flags & self::SYNTAX_PRINT_VANILLA) {
				throw new \RuntimeException("Collided flag."); //TODO: BETTER MSG
			}
			$message = self::OVO_GENERIC_SYNTAX_MESSAGE;
			$translate = [
				"previous" => $parts[0], "broken_syntax" => $result->getBrokenSyntax(), "after" => $parts[1],
			];
			return self::translate($message, $translate);
		}
		if ($flags & self::SYNTAX_PRINT_VANILLA) {
			return self::parseVanillaSyntaxMessage($parts[0], $brokenPart, $parts[1]);
		}
		throw new \RuntimeException("MSG"); //TODO: Better msg
	}

	public static function parseVanillaSyntaxMessage(string $previous, string $brokenPart, string $after) : string{
		return (new Translatable(self::COMMAND_GENERIC_SYNTAX_KEY, [$previous, $brokenPart, $after]))->getText();
	}

	private static function vanillaShift(string $in) : string{
		return substr($in, -9);
	}

	/**
	 * @param array<string, string> $tags
	 */
	private static function translate(string $msg, array $tags) : string{
		foreach ($tags as $tag => $value) {
			$msg = str_replace('{' . $tag . '}', $value, $msg);
		}
		return $msg;
	}

	/**
	 * @return string[]
	 */
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
