<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

class FloatParameter extends BaseParameter{
	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::FLOAT;
	}

	public function getValueName() : string{ return "float"; }

	public function parse(array $parameters) : ValueResult|BrokenSyntaxResult{
		$f = implode("", $parameters);
		if (is_numeric($f)) {
			return ValueResult::create((float) $f);
		}
		return BrokenSyntaxResult::create($f);
	}
}
