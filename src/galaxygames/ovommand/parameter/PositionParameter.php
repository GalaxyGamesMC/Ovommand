<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\CoordinateResult;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

class PositionParameter extends BaseParameter{
	public function getValueName() : string{
		return "x y z";
	}

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::POSITION;
	}

	public function parse(array $parameters) : BrokenSyntaxResult|CoordinateResult{
		$newParameters = [];
		foreach ($parameters as $parameter) { //special case, ~~~
			$s = $parameter;
			$l = strlen($s);

			$currentPos = 0;

			while ($currentPos < $l) {
				$tildePos = strpos($s, "~", $currentPos + 1);
				$caretPos = strpos($s, "^", $currentPos + 1);
				if ($tildePos === false) {
					$tildePos = $l;
				}
				if ($caretPos === false) {
					$caretPos = $l;
				}
				$nextPos = min($tildePos, $caretPos);

				$newParameters[] = substr($s, $currentPos, $nextPos - $currentPos);
				$currentPos = $nextPos;
			}
		}
		$parameters = $newParameters;
		$pCount = count($parameters);

		if ($pCount > $this->getSpanLength()) {
			return BrokenSyntaxResult::create($parameters[$this->getSpanLength()], implode(" ", $parameters))
				->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS);
		}

		$brokenSyntax = "";
		$coordType = null;
		$types = [];
		$values = [];
		$match = 0;

		foreach ($parameters as $i => $parameter) {
			if (str_contains($parameter, " ")) {
				$brokenSyntax = $parameter;
				break;
			}
			// ^([~^]?[+-]?\d*?(?:\.\d+)?)[ ]?([~^]?[+-]?\d*?(?:\.\d+)?)[ ]?([~^]?[+-]?\d*?(?:\.\d+)?)$
			if (!preg_match("/^([~^]?[+-]?\d*(?:\.\d+)?)$/", $parameter)) {
				$brokenSyntax = $parameter;
				break;
			}
			$type = match ($u = $parameter[0]) {
				"~" => CoordinateResult::TYPE_RELATIVE,
				"^" => CoordinateResult::TYPE_LOCAL,
				default => CoordinateResult::TYPE_DEFAULT
			};
			if ($coordType === null) {
				$coordType = $type;
			}
			if ($type === CoordinateResult::TYPE_LOCAL && $coordType !== CoordinateResult::TYPE_LOCAL) {
				$brokenSyntax = $parameter;
				break;
			}
			if ($coordType === CoordinateResult::TYPE_LOCAL && $type !== CoordinateResult::TYPE_LOCAL) {
				$brokenSyntax = $parameter;
				break;
			}
			$match++;
			$value = ltrim($parameter, $u);
			$values[$i] = str_contains($value, ".") ? (double) $value : (int) $value;
			$types[$i] = $type;
		}
		if ($brokenSyntax !== "") {
			return BrokenSyntaxResult::create($brokenSyntax, implode(" ", $parameters), $this->getValueName())->setMatchedParameter($match)->setRequiredParameter($this->getSpanLength());
		}
		if ($pCount < $this->getSpanLength()) {
			return BrokenSyntaxResult::create($brokenSyntax, implode(" ", $parameters), $this->getValueName())
				->setMatchedParameter($match)
				->setRequiredParameter($this->getSpanLength())
				->setCode(BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS);
		}
		if ($match < $this->getSpanLength()) {
			return BrokenSyntaxResult::create($brokenSyntax, implode(" ", $parameters), $this->getValueName())
				->setMatchedParameter($match)
				->setRequiredParameter($this->getSpanLength());
		}
		return CoordinateResult::fromData(...$values, ...$types);
	}

	// beta is faster than most case compared to the above
	public function betaParse(array $parameters) : BaseResult{
		$parameter = implode(" ", $parameters);
		if (!preg_match("/^([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?)$/", $parameter, $matches)) {
			return new BrokenSyntaxResult($parameter);
		}

		if (empty($matches)) {
			return new BrokenSyntaxResult("");
		}

		$typeCast = static function(string $in) {
			return match ($u = $in[0]) {
				"~" => CoordinateResult::TYPE_RELATIVE,
				"^" => CoordinateResult::TYPE_LOCAL,
				default => CoordinateResult::TYPE_DEFAULT
			};
		};
		$xType = $typeCast($matches[1][0]);
		$xValue = ltrim($matches[1], $matches[1][0]);
		$x = str_contains($xValue, ".") ? (double) $xValue : (int) $xValue;
		$yType = $typeCast($matches[2][0]);
		$yValue = ltrim($matches[2], $matches[2][0]);
		$y = str_contains($yValue, ".") ? (double) $yValue : (int) $yValue;
		$zType = $typeCast($matches[3][0]);
		$zValue = ltrim($matches[3], $matches[2][0]);
		$z = str_contains($zValue, ".") ? (double) $zValue : (int) $zValue;
		$hasCaret = $xType === CoordinateResult::TYPE_LOCAL || $yType === CoordinateResult::TYPE_LOCAL || $zType === CoordinateResult::TYPE_LOCAL;
		if (!($xType === CoordinateResult::TYPE_LOCAL && $yType === CoordinateResult::TYPE_LOCAL && $zType === CoordinateResult::TYPE_LOCAL) && $hasCaret) {
			return new BrokenSyntaxResult("");
		}

		return new CoordinateResult($x, $y, $z, $xType, $yType, $zType);
	}

	public function getSpanLength() : int{
		return 3;
	}
}
