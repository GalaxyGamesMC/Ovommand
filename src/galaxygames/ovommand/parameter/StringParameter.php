<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;

class StringParameter extends BaseParameter{
	public function getValueName() : string{ return "string"; }

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::STRING;
	}

	public function parse(array $parameters) : ValueResult|BrokenSyntaxResult{
		$result = parent::parse($parameters);
		if ($result instanceof BrokenSyntaxResult) {
			return $result;
		}
		return ValueResult::create($parameters[0]);
	}
}
