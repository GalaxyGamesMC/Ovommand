<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BaseResult;
use galaxygames\ovommand\parameter\result\ErrorResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use galaxygames\ovommand\parameter\type\ParameterTypes;

class FloatParameter extends BaseParameter{
	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::FLOAT;
	}

	public function getValueName() : string{
		return "float";
	}

	public function parse(array $parameters) : BaseResult{
		$f = implode("", $parameters);
		if (is_numeric($f)) {
			return ValueResult::create((float) $f);
		}
		return ErrorResult::create("$f is not a float number!"); //TODO: better msg
	}
}
