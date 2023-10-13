<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\CoordinateResult;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

class BlockPositionParameter extends BaseParameter{
	public function getValueName() : string{ return "x y z"; }

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::BLOCK_POSITION;
	}

	public function parse(array $parameters) : BaseResult{
		$parameter = implode(" ", $parameters);
		if (!preg_match_all("/([^~^+\-\s\d]+)?([~^]?[+-]?\d+|[~^])([^~^\s\d]+)?/", $parameter, $matches)) {
			return BrokenSyntaxResult::create($parameter, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		if (count($matches[0]) < 3) {
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
					->setMatchedParameter($i)
					->setRequiredParameter($this->getSpanLength());
			}
		}
		$xPreInvalid = $matches[1][0];
		if (!empty($xPreInvalid)) {
			return BrokenSyntaxResult::create($xPreInvalid, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		$xPostInvalid = $matches[3][0];
		if (!empty($xPostInvalid)) {
			return BrokenSyntaxResult::create($xPostInvalid, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX)
				->setMatchedParameter(1);
		}
		$yPreInvalid = $matches[1][1];
		if (!empty($yPreInvalid)) {
			return BrokenSyntaxResult::create($yPreInvalid, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX)
				->setMatchedParameter(1);
		}
		$yPostInvalid = $matches[3][1];
		if (!empty($yPostInvalid)) {
			return BrokenSyntaxResult::create($yPostInvalid, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX)
				->setMatchedParameter(2);
		}
		$zPreInvalid = $matches[1][2];
		if (!empty($zPreInvalid)) {
			return BrokenSyntaxResult::create($zPreInvalid, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX)
				->setMatchedParameter(2);
		}
		$zPostInvalid = $matches[3][2];
		if (!empty($zPostInvalid)) {
			return BrokenSyntaxResult::create($zPostInvalid, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX)
				->setMatchedParameter(3);
		}
		$x = (float) substr($matches[2][0], 1);
		$y = (float) substr($matches[2][1], 1);
		$z = (float) substr($matches[2][2], 1);
		return CoordinateResult::fromData($x, $y, $z, $xType, $yType, $zType);
	}

	public function getSpanLength() : int{
		return 3;
	}
}
