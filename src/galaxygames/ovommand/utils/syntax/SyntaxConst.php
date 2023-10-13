<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils\syntax;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use pocketmine\lang\Translatable;

class SyntaxConst{
	public const SYNTAX_PRINT_FULL = 0; // previous>>error<<after
	public const SYNTAX_PRINT_VANILLA = 1; // 123456789>>error<<123456789
	public const SYNTAX_PRINT_OVO_FULL = 2;
	public const SYNTAX_PRINT_OVO_VANILLA = 3;
	public const SYNTAX_PRINT_USAGE = 4;

	protected static int $syntax = self::SYNTAX_PRINT_FULL;

	public const COMMAND_GENERIC_SYNTAX_KEY = "commands.generic.syntax";
	public const OVO_GENERIC_SYNTAX_MESSAGE = "Syntax error: Unexpected \"{broken_syntax}\": at \"{previous}>>{broken_syntax}<<{after}\"";
	public const OVO_GENERIC_SYNTAX_HELPER_MESSAGE = ". Suggest: \"{helps}\"";

	public static function parseSyntax(string $previous, string $brokenSyntax, string $after, string $helps = "") : string|null{
		if (self::$syntax === self::SYNTAX_PRINT_USAGE) {
			return null;
		}
		return match (self::$syntax) {
			self::SYNTAX_PRINT_FULL => self::parseVanillaSyntaxMessage($previous, $brokenSyntax, $after),
			self::SYNTAX_PRINT_VANILLA => self::parseVanillaSyntaxMessage(self::vanillaShift($previous), $brokenSyntax, self::vanillaShift($after)),
			self::SYNTAX_PRINT_OVO_FULL => self::parseOvommandSyntaxMessage($previous, $brokenSyntax, $after, $helps),
			self::SYNTAX_PRINT_OVO_VANILLA => self::parseOvommandSyntaxMessage(self::vanillaShift($previous), $brokenSyntax, self::vanillaShift($after), $helps),
			default => $previous . " " . $brokenSyntax . " " . $after
		};
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

	private static function vanillaShift(string $in) : string{
		return substr($in, -9);
	}

	public static function parseVanillaSyntaxMessage(string $previous, string $brokenSyntax, string $after) : string{
		return (new Translatable(self::COMMAND_GENERIC_SYNTAX_KEY, [$previous, $brokenSyntax, $after]))->getText();
	}

	public static function parseOvommandSyntaxMessage(string $previous, string $brokenSyntax, string $after, string $helps = "") : string{
		$translate = [
			"previous" => $previous, "broken_syntax" => $brokenSyntax, "after" => $after,
		];
		if ($helps === "") {
			return self::translate(self::OVO_GENERIC_SYNTAX_MESSAGE, $translate);
		}
		$translate["helps"] = $helps;
		return self::translate(self::OVO_GENERIC_SYNTAX_MESSAGE . self::OVO_GENERIC_SYNTAX_HELPER_MESSAGE, $translate);
	}

	public static function parseFromBrokenSyntaxResult(BrokenSyntaxResult $result) : string{
		return $result->getBrokenSyntax();
	}

	/**
	 * @phpstan-param array<string, string> $tags
	 */
	private static function translate(string $msg, array $tags) : string{
		foreach ($tags as $tag => $value) {
			$msg = str_replace('{' . $tag . '}', $value, $msg);
		}
		return $msg;
	}

	public static function getSyntax() : int{
		return self::$syntax;
	}

	public static function setSyntax(int $syntax) : void{
		self::$syntax = $syntax;
	}
}
