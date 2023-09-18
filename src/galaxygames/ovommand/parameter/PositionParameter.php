<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\CoordinateResult;
use galaxygames\ovommand\parameter\result\ErrorResult;
use galaxygames\ovommand\parameter\type\ParameterTypes;
use galaxygames\ovommand\syntax\SyntaxConst;

class PositionParameter extends BaseParameter{
	public function getName() : string{
		return "x y z";
	}

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::POSITION;
	}

	public function parse(array $parameters) : ErrorResult|CoordinateResult{
		if (count($parameters) > $this->getSpanLength()) {
			throw new \InvalidArgumentException("Too many args");
		}
		$brokenSyntax = "";
		$genType = null;
		$types = [];
		$values = [];
		foreach ($parameters as $i => $parameter) {
			if (str_contains($parameter, " ")) {
				$brokenSyntax = $parameter;
				break;
			}
			if (!preg_match("/^([~^]?[+-]?\d+(?:\.\d+)?)$/", $parameter)) {
				$brokenSyntax = $parameter;
				break;
			}
			$type = match($u = substr($parameter, 0, 1)) {
				"~" => CoordinateResult::TYPE_RELATIVE,
				"^" => CoordinateResult::TYPE_LOCAL,
				default => CoordinateResult::TYPE_DEFAULT
			};
			if ($genType === null) {
				$genType = $type;
			}
			if ($type === CoordinateResult::TYPE_LOCAL && $genType !== CoordinateResult::TYPE_LOCAL) {
				$brokenSyntax = $parameter;
				break;
			}
			if ($genType === CoordinateResult::TYPE_LOCAL && $type !== CoordinateResult::TYPE_LOCAL) {
				$brokenSyntax = $parameter;
				break;
			}
			$value = ltrim($parameter, $u);
			$nValue = str_contains($value, ".") ? (double) $value : (int) $value;
			$types[$i] = $type;
			$values[$i] = $nValue;
		}
		if ($brokenSyntax !== "") {
			$syntax = SyntaxConst::getSyntaxBetweenBrokenPart(implode(" ", $parameters), $brokenSyntax);
			return ErrorResult::create(SyntaxConst::parseSyntax($syntax[0], $brokenSyntax, $syntax[1]));
		}
		return CoordinateResult::fromData(...$values, ...$types);
	}

	public function getSpanLength() : int{
		return 3;
	}
}
