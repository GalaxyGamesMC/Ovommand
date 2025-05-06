<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;

class FloatParameter extends BaseParameter{
	public function getNetworkType() : ParameterTypes{ return ParameterTypes::FLOAT; }
	public function getValueName() : string{ return "float"; }

	public function parse(array $parameters) : ValueResult|BrokenSyntaxResult{
		$result = parent::parse($parameters);
		if ($result instanceof BrokenSyntaxResult) {
			return $result;
		}
		if (is_numeric($parameters[0])) {
			return ValueResult::create((float) $parameters[0]);
		}
		return BrokenSyntaxResult::create($parameters[0], $parameters[0]);
	}
}
