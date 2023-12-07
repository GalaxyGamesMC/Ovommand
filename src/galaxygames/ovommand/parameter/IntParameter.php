<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;

class IntParameter extends BaseParameter{
	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::INT;
	}

	public function parse(array $parameters) : ValueResult|BrokenSyntaxResult{
		$result = parent::parse($parameters);
		if ($result instanceof BrokenSyntaxResult) {
			return $result;
		}
		if (preg_match("/^\d+$/", $parameters[0])) {
			return ValueResult::create((int) $parameters[0]);
		}
		return BrokenSyntaxResult::create($parameters[0], $parameters[0]);
	}

	public function getValueName() : string{ return "int"; }
}
