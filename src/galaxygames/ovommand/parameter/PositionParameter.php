<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\CoordinateResult;
use galaxygames\ovommand\utils\syntax\SyntaxConst;

class PositionParameter extends BaseParameter{
	public function getValueName() : string{
		return "x y z";
	}

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::POSITION;
	}

	public function parse(array $parameters) : BrokenSyntaxResult|CoordinateResult{
//		parent::parse($parameters);

		$pCount = count($parameters);

		if ($pCount < $this->getSpanLength()) {
			return new BrokenSyntaxResult("");
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
			if (!preg_match("/^([~^]?[+-]?\d+(?:\.\d+)?)$/", $parameter)) {
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
			$value = ltrim($parameter, $u);
			$nValue = str_contains($value, ".") ? (double) $value : (int) $value;
			$match++;
			$types[$i] = $type;
			$values[$i] = $nValue;
		}
		if ($match < $this->getSpanLength()) {
			return  BrokenSyntaxResult::create(implode(" ", $parameters), expectedType: $this->getValueName());
		}
		if ($brokenSyntax !== "") {
			$syntax = SyntaxConst::getSyntaxBetweenBrokenPart(implode(" ", $parameters), $brokenSyntax);
			return BrokenSyntaxResult::create(SyntaxConst::parseSyntax($syntax[0] ?? "", $brokenSyntax, $syntax[1] ?? "") ?? "")->setMatchedParameter($match);
		}
		return CoordinateResult::fromData(...$values, ...$types);
	}

	public function getSpanLength() : int{
		return 3;
	}
}
