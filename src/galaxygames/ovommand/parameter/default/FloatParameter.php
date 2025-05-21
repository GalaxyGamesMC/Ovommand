<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\default;

use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\ParameterTypes;
use galaxygames\ovommand\parameter\parser\ParameterParser;
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
		return ParameterParser::parseFloat($parameters[0]);
	}
}
