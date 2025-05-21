<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\default;

use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\ParameterTypes;
use galaxygames\ovommand\parameter\parser\ParameterParser;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;

class IntParameter extends BaseParameter{
	public function getValueName() : string{ return "int"; }
	public function getNetworkType() : ParameterTypes{ return ParameterTypes::INT; }

	public function parse(array $parameters) : ValueResult|BrokenSyntaxResult{
		$result = parent::parse($parameters);
		if ($result instanceof BrokenSyntaxResult) {
			return $result;
		}
		return ParameterParser::parseInt($parameters[0]);
	}
}
