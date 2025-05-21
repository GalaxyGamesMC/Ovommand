<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\parser;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\CoordinateResult;
use galaxygames\ovommand\parameter\result\ValueResult;

/**
 * @phpstan-type TRCapture array{string,int<-1,max>}
 * @phpstan-type TRMatch array<TRCapture>
 * @phpstan-type TRMatchAll array<TRMatch>
 */
class ParameterParser{
	public const REGEX_FLOAT = "/([^\d+-.]*)([+-]?\d*\.?\d+)(.*)/";
	public const REGEX_INT = "/([^\d+-]*)([+-]?\d+)(.*)/";
	public const REGEX_INT2 = "/\s*([^\d+-]*)([+-]?\d+)(.*)/";
	public const REGEX_INT3 = "/\s*([^\d+-]*)([+-]?\d+)\s*(.*)/";
	public const REGEX_BLOCK_POSITION = "/([+-]+[^\d~^]+|[^\d~^+-]*)([~^]?[+-]?\d+|[~^])([+-]+[^\d~^]+|[^\d~^+-]*)/";
	public const REGEX_POSITION = "/([+-]+[^\d~^]+|[^\d~^+-]*)([~^]?[+-]?\d*\.?\d+|[~^])([+-]+[^\d~^]+|[^\d~^+-]*)/";
	public const REGEX_POSITION2 = "/([^\d\s~^+-]*(?:[+-]+[^\d~^]+)?)([~^]?[+-]?\d*\.?\d+|[~^])([^\d\s~^+-]*(?:[+-]+[^\d~^]+)?)/";
	public const REGEX_POSITION3 = "/([+-]+[^~^\d]+|[^~\d^+-]*)([~^]?[+-]?\d*\.?\d+|~|\^)([+-]+[^~\d]+|[^~\d^+-]*)(?=[+-]+[^~^\d]+|[^~\d^+-]*|$)/";

	public static function parseFloat(string $value) : BrokenSyntaxResult | ValueResult{
		if (!preg_match(self::REGEX_FLOAT, $value, $matches, PREG_OFFSET_CAPTURE)) {
			return BrokenSyntaxResult::create($value, expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		/** @var TRMatch $matches */
		$preInvalid = !empty(ltrim($matches[1][0]));
		if ($preInvalid) {
			return BrokenSyntaxResult::create($value, $matches[1][0], $matches[1][1], expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		$postInvalid = !empty(ltrim($matches[3][0]));
		if ($postInvalid) {
			return BrokenSyntaxResult::create($value, $matches[3][0], $matches[3][1], expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		return ValueResult::create((float) $matches[2][0]);
	}

	public static function parseInt(string $value) : BrokenSyntaxResult | ValueResult{
		if (!preg_match(self::REGEX_INT, $value, $matches, PREG_OFFSET_CAPTURE)) {
			return BrokenSyntaxResult::create($value, expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		/** @var TRMatch $matches */
		$preInvalid = !empty(ltrim($matches[1][0]));
		if ($preInvalid) {
			return BrokenSyntaxResult::create($value, $matches[1][0], $matches[1][1], expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		$postInvalid = !empty(ltrim($matches[3][0]));
		if ($postInvalid) {
			return BrokenSyntaxResult::create($value, $matches[3][0], $matches[3][1], expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		return ValueResult::create((int) $matches[2][0]);
	}

	public static function parseInt2(string $value) : BrokenSyntaxResult | ValueResult{
		if (!preg_match(self::REGEX_INT2, $value, $matches, PREG_OFFSET_CAPTURE)) {
			return BrokenSyntaxResult::create($value, expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		/** @var TRMatch $matches */
		$preInvalid = !empty($matches[1][0]);
		if ($preInvalid) {
			return BrokenSyntaxResult::create($value, $matches[1][0], $matches[1][1], expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		$postInvalid = !empty(ltrim($matches[3][0]));
		if ($postInvalid) {
			return BrokenSyntaxResult::create($value, $matches[3][0], $matches[3][1], expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		return ValueResult::create((int) $matches[2][0]);
	}

	public static function parseInt3(string $value) : BrokenSyntaxResult | ValueResult{
		if (!preg_match(self::REGEX_INT3, $value, $matches, PREG_OFFSET_CAPTURE)) {
			return BrokenSyntaxResult::create($value, expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		/** @var TRMatch $matches */
		$preInvalid = !empty($matches[1][0]);
		if ($preInvalid) {
			return BrokenSyntaxResult::create($value, $matches[1][0], $matches[1][1], expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		$postInvalid = !empty($matches[3][0]);
		if ($postInvalid) {
			return BrokenSyntaxResult::create($value, $matches[3][0], $matches[3][1], expectedType: "float")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		return ValueResult::create((int) $matches[2][0]);
	}

	public static function positionTagCast(string $value) : int{
		return match ($value) {
			"~" => CoordinateResult::TYPE_RELATIVE,
			"^" => CoordinateResult::TYPE_LOCAL,
			default => CoordinateResult::TYPE_DEFAULT
		};
	}

	public static function parseBlockPosition(string $value) : BrokenSyntaxResult | CoordinateResult{
		if (!preg_match_all(self::REGEX_BLOCK_POSITION, $value, $matches, PREG_OFFSET_CAPTURE)) {
			return BrokenSyntaxResult::create($value, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX); // only do this with input being a continuous word
		}
		/** @var TRMatchAll $matches */
//		dump($matches);
		$matchCount = count($matches[0]);
		if ($matchCount < 3) {
			return BrokenSyntaxResult::create($value, "", strlen($value), expectedType: "position")->setCode(BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS);
		}
		for ($i = 0; $i < 3; $i++) {
			$preInvalid = !empty(ltrim($matches[1][$i][0]));
			$postInvalid = !empty(ltrim($matches[3][$i][0]));
			if ($preInvalid) {
				return BrokenSyntaxResult::create($value, $matches[1][$i][0], $matches[1][$i][1], expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
			if ($postInvalid) {
				return BrokenSyntaxResult::create($value, $matches[3][$i][0], $matches[3][$i][1], expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
		}

		if ($matchCount > 3) {
			return BrokenSyntaxResult::create($value, substr($value, $matches[2][2][1] + 1), $matches[2][2][1] + 1, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS);
		}

		$xType = self::positionTagCast($matches[2][0][0][0]);
		$yType = self::positionTagCast($matches[2][1][0][0]);
		$zType = self::positionTagCast($matches[2][2][0][0]);

		$coordType = null;
		foreach ([$xType, $yType, $zType] as $i => $type) {
			if ($coordType === null) {
				$coordType = $type;
			}
			if ($type !== $coordType && ($type === CoordinateResult::TYPE_LOCAL || $coordType === CoordinateResult::TYPE_LOCAL)) {
				return BrokenSyntaxResult::create($value, $matches[2][$i][0][0],$matches[2][$i][1] + 1, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
			if ($type !== CoordinateResult::TYPE_DEFAULT) {
				$matches[2][$i][0] = substr($matches[2][$i][0], 1);
			}
		}
		return CoordinateResult::create((float) $matches[2][0][0], (float) $matches[2][1][0], (float) $matches[2][2][0], $xType, $yType, $zType);
	}

	public static function parsePosition(string $value) : BrokenSyntaxResult | CoordinateResult{
		if (!preg_match_all(self::REGEX_POSITION, $value, $matches, PREG_OFFSET_CAPTURE)) {
			return BrokenSyntaxResult::create($value, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX); // only do this with input being a continuous word
		}
		/** @var TRMatchAll $matches */
		$matchCount = count($matches[0]);
		if ($matchCount < 3) {
			return BrokenSyntaxResult::create($value, "", strlen($value), expectedType: "position")->setCode(BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS);
		}
		for ($i = 0; $i < 3; $i++) {
			$preInvalid = !empty(ltrim($matches[1][$i][0]));
			$postInvalid = !empty(ltrim($matches[3][$i][0]));
			if ($preInvalid) {
				return BrokenSyntaxResult::create($value, $matches[1][$i][0], $matches[1][$i][1], expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
			if ($postInvalid) {
				return BrokenSyntaxResult::create($value, $matches[3][$i][0], $matches[3][$i][1], expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
		}

		if ($matchCount > 3) {
			return BrokenSyntaxResult::create($value, substr($value, $matches[2][2][1] + 1), $matches[2][2][1] + 1, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS);
		}

		$xType = self::positionTagCast($matches[2][0][0][0]);
		$yType = self::positionTagCast($matches[2][1][0][0]);
		$zType = self::positionTagCast($matches[2][2][0][0]);

		$coordType = null;
		foreach ([$xType, $yType, $zType] as $i => $type) {
			if ($coordType === null) {
				$coordType = $type;
			}
			if ($type !== $coordType && ($type === CoordinateResult::TYPE_LOCAL || $coordType === CoordinateResult::TYPE_LOCAL)) {
				return BrokenSyntaxResult::create($value, $matches[2][$i][0][0],$matches[2][$i][1] + 1, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
			if ($type !== CoordinateResult::TYPE_DEFAULT) {
				$matches[2][$i][0] = substr($matches[2][$i][0], 1);
			}
		}
		return CoordinateResult::create((float) $matches[2][0][0], (float) $matches[2][1][0], (float) $matches[2][2][0], $xType, $yType, $zType);
	}

	public static function parsePosition2(string $value) : BrokenSyntaxResult | CoordinateResult{
		// "([^\d\s~^+-]*(?:[+-]+[^\d~^]+)?)([~^]?[+-]?\d*\.?\d+|[~^])([^\d\s~^+-]*(?:[+-]+[^\d~^]+)?)"
		if (!preg_match_all(self::REGEX_POSITION2, $value, $matches, PREG_OFFSET_CAPTURE)) {
//		if (!preg_match_all("/([^\d\s~^+-]*(?:[+-]+[^\d~]+)?)([~^]?[+-]?\d+\.?\d*|[~^])([^\d\s~^+-]*(?:[+-]+[^\d~]+)?)/", $value, $matches, PREG_OFFSET_CAPTURE)) {
			return BrokenSyntaxResult::create($value, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		/** @var TRMatchAll $matches */
		$matchCount = count($matches[0]);
		if ($matchCount < 3) {
			return BrokenSyntaxResult::create($value, "", strlen($value), expectedType: "position")->setCode(BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS);
		}
		for ($i = 0; $i < 3; $i++) {
			$preInvalid = !empty($matches[1][$i][0]);
			$postInvalid = !empty($matches[3][$i][0]);
			if ($preInvalid) {
				return BrokenSyntaxResult::create($value, $matches[1][$i][0], $matches[1][$i][1], expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
			if ($postInvalid) {
				return BrokenSyntaxResult::create($value, $matches[3][$i][0], $matches[3][$i][1], expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
		}

		if ($matchCount > 3) {
			return BrokenSyntaxResult::create($value, substr($value, $matches[2][2][1] + 1), $matches[2][2][1] + 1, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS);
		}

		$xType = self::positionTagCast($matches[2][0][0][0]);
		$yType = self::positionTagCast($matches[2][1][0][0]);
		$zType = self::positionTagCast($matches[2][2][0][0]);

		$coordType = null;
		foreach ([$xType, $yType, $zType] as $i => $type) {
			if ($coordType === null) {
				$coordType = $type;
			}
			if ($type !== $coordType && ($type === CoordinateResult::TYPE_LOCAL || $coordType === CoordinateResult::TYPE_LOCAL)) {
				return BrokenSyntaxResult::create($value, $matches[2][$i][0][0],$matches[2][$i][1] + 1, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
			if ($type !== CoordinateResult::TYPE_DEFAULT) {
				$matches[2][$i][0] = substr($matches[2][$i][0], 1);
			}
		}
		return CoordinateResult::create((float) $matches[2][0][0], (float) $matches[2][1][0], (float) $matches[2][2][0], $xType, $yType, $zType);
	}

	public static function parsePosition3(string $value) : BrokenSyntaxResult | CoordinateResult{
		if (!preg_match_all(self::REGEX_POSITION3, $value, $matches, PREG_OFFSET_CAPTURE)) {
			return BrokenSyntaxResult::create($value, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		/** @var TRMatchAll $matches */
//		dump($matches);
		$matchCount = count($matches[0]);
		if ($matchCount < 3) {
			return BrokenSyntaxResult::create($value, "", strlen($value), expectedType: "position")->setCode(BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS);
		}
		for ($i = 0; $i < 3; $i++) {
			$preInvalid = !empty($matches[1][$i][0]);
			$postInvalid = !empty($matches[3][$i][0]);
			if ($preInvalid) {
				return BrokenSyntaxResult::create($value, $matches[1][$i][0], $matches[1][$i][1], expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
			if ($postInvalid) {
				return BrokenSyntaxResult::create($value, $matches[3][$i][0], $matches[3][$i][1], expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
		}

		if ($matchCount > 3) {
			return BrokenSyntaxResult::create($value, substr($value, $matches[2][2][1] + 1), $matches[2][2][1] + 1, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS);
		}

		$xType = self::positionTagCast($matches[2][0][0][0]);
		$yType = self::positionTagCast($matches[2][1][0][0]);
		$zType = self::positionTagCast($matches[2][2][0][0]);

		$coordType = null;
		foreach ([$xType, $yType, $zType] as $i => $type) {
			if ($coordType === null) {
				$coordType = $type;
			}
			if ($type !== $coordType && ($type === CoordinateResult::TYPE_LOCAL || $coordType === CoordinateResult::TYPE_LOCAL)) {
				return BrokenSyntaxResult::create($value, $matches[2][$i][0][0],$matches[2][$i][1] + 1, expectedType: "position")->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
			}
			if ($type !== CoordinateResult::TYPE_DEFAULT) {
				$matches[2][$i][0] = substr($matches[2][$i][0], 1);
			}
		}
		return CoordinateResult::create((float) $matches[2][0][0], (float) $matches[2][1][0], (float) $matches[2][2][0], $xType, $yType, $zType);
	}
}