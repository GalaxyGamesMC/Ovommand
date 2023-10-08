<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\CoordinateResult;
use RuntimeException;
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
//			if ($type === CoordinateResult::TYPE_LOCAL && $coordType !== CoordinateResult::TYPE_LOCAL) {
//				$brokenSyntax = $parameter;
//				break;
//			}
//			if ($coordType === CoordinateResult::TYPE_LOCAL && $type !== CoordinateResult::TYPE_LOCAL) {
//				$brokenSyntax = $parameter;
//				break;
//			}
			if ($type !== $coordType && ($type === CoordinateResult::TYPE_LOCAL || $coordType === CoordinateResult::TYPE_LOCAL)) {
				$brokenSyntax = $parameter;
				break; // this would shorten the two above?
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

	// beta is faster than most case compared to the above, except for large input, eg: ~1327819873199127309273197392709273927832981720371987321893~1.13133131313131313~10987310923810983109813093809128109281

	/**
	 * @param string[] $parameters
	 */
	public function betaParse(array $parameters) : BaseResult{
		$parameter = implode(" ", $parameters);
		if (!preg_match("/^([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?) *([~^]?[+-]?\d*?(?:\.\d+)?)$/U", $parameter, $matches)) {
			return BrokenSyntaxResult::create($parameter, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
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
		$x = (double) $xValue;
//		$x = str_contains($xValue, ".") ? (double) $xValue : (int) $xValue;
		$yType = $typeCast($matches[2][0]);
		$yValue = ltrim($matches[2], $matches[2][0]);
		$y = (double) $yValue;
//		$y = str_contains($yValue, ".") ? (double) $yValue : (int) $yValue;
		$zType = $typeCast($matches[3][0]);
		$zValue = ltrim($matches[3], $matches[2][0]);
		$z = (double) $zValue;
//		$z = str_contains($zValue, ".") ? (double) $zValue : (int) $zValue;
		$hasCaret = $xType === CoordinateResult::TYPE_LOCAL || $yType === CoordinateResult::TYPE_LOCAL || $zType === CoordinateResult::TYPE_LOCAL;
		if (!($xType === CoordinateResult::TYPE_LOCAL && $yType === CoordinateResult::TYPE_LOCAL && $zType === CoordinateResult::TYPE_LOCAL) && $hasCaret) {
			return new BrokenSyntaxResult("");
		}

		return new CoordinateResult($x, $y, $z, $xType, $yType, $zType);
	}

	/**
	 * @param string[] $parameters
	 */
	public function omegaParse(array $parameters) : BaseResult{
		$parameter = implode(" ", $parameters);

		//		if (!preg_match_all("/(\S[~^]?[+-]?(?:\d*\.\d+|\d+)?)([^~\s\d]+)?/", $parameter, $matches)) {
//		if (!preg_match_all("/([~^]?[+-]?(?:\d*\.\d+|\d+)?)([^~\s\d]+)?/", $parameter, $matches)) {
		if (!preg_match_all("/([~^]?[+-]?(?:\d*\.\d+|\d+)|[~^])([^~\s\d]+)?/", $parameter, $matches)) {
//			echo "Meow";
			return BrokenSyntaxResult::create($parameter, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX);
		}
		if (($u = count($matches[0])) < 3) {
//			echo "Meow2";
			return BrokenSyntaxResult::create("", $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS);
		}
		$typeCast = static function(string $in) {
			return match ($u = $in[0]) {
				"~" => CoordinateResult::TYPE_RELATIVE,
				"^" => CoordinateResult::TYPE_LOCAL,
				default => CoordinateResult::TYPE_DEFAULT
			};
		};
		$xInvalid = $matches[2][0];
		if (!empty($xInvalid)) {
			return BrokenSyntaxResult::create($xInvalid, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX)
				->setMatchedParameter(1);
		}
		$yInvalid = $matches[2][1];
		if (!empty($yInvalid)) {
			return BrokenSyntaxResult::create($yInvalid, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX)
				->setMatchedParameter(2);
		}
		$zInvalid = $matches[2][2];
		if (!empty($zInvalid)) {
			return BrokenSyntaxResult::create($zInvalid, $parameter, $this->getValueName())
				->setCode(BrokenSyntaxResult::CODE_BROKEN_SYNTAX)
				->setMatchedParameter(3);
		}
		$xType = $typeCast($matches[1][0][0]);
		$x = (float) substr($matches[1][0], 1);
		$yType = $typeCast($matches[1][1][0]);
		$y = (float) substr($matches[1][1], 1);
		$zType = $typeCast($matches[1][1][0]);
		$z = (float) substr($matches[1][2], 1);
		return CoordinateResult::fromData($x, $y, $z, $xType, $yType, $zType);
	}

	public function getSpanLength() : int{
		return 3;
	}
}
