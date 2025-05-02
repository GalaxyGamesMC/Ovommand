<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\CoordinateResult;

class PositionParameter extends BaseParameter{
	public function getValueName() : string{ return "x y z"; }

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::POSITION;
	}

	public function hasCompactParameter() : bool{
		return true;
	}

	public function parse(array $parameters) : CoordinateResult|BrokenSyntaxResult{
		$parameter = implode(" ", $parameters);
		if (!preg_match_all("/([^~^+\-\d\s]+)?([~^]?[+-]?(\d+(?:\.\d+)?)|[~^])([[:blank:]]?[^~^+\-\d\s]+)?/", $parameter, $matches)) {
			return BrokenSyntaxResult::create($parameter, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		$matchCount = count($matches[0]);
		if ($matchCount < 3) {
			return BrokenSyntaxResult::create("", $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS);
		}
		$typeCast = static function(string $in){
			return match ($in[0]) {
				"~" => CoordinateResult::TYPE_RELATIVE,
				"^" => CoordinateResult::TYPE_LOCAL,
				default => CoordinateResult::TYPE_DEFAULT
			};
		};
		$xType = $typeCast($matches[2][0][0]);
		$yType = $typeCast($matches[2][1][0]);
		$zType = $typeCast($matches[2][2][0]);
		$coordType = null;
		foreach ([$xType, $yType, $zType] as $i => $type) {
			if ($coordType === null) {
				$coordType = $type;
			}
			if ($type !== $coordType && ($type === CoordinateResult::TYPE_LOCAL || $coordType === CoordinateResult::TYPE_LOCAL)) {
				return BrokenSyntaxResult::create($matches[2][$i], $parameter, $this->getValueName())
					->setMatchedParameter($i);
			}
		}
		$xPreInvalid = $matches[1][0];
		if (!empty($xPreInvalid)) {
			return BrokenSyntaxResult::create($xPreInvalid, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		for ($i = 1; $i < 3; $i++) {
			if (empty($matches[2][$i])) {
				return BrokenSyntaxResult::create("", $parameter, $this->getValueName())
					->setCode(BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS);
			}
		}
		if ($matchCount > 3) {
			return BrokenSyntaxResult::create($matches[0][3], $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS)->setMatchedParameter(3);
		}
		return CoordinateResult::fromData((float) $matches[3][0], (float) $matches[3][1], (float) $matches[3][2], $xType, $yType, $zType);
	}

	public function getSpanLength() : int{
		return 3;
	}
}
