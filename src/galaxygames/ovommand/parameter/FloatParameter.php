<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BaseResult;
use galaxygames\ovommand\parameter\type\ParameterTypes;

class FloatParameter extends BaseParameter{
	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::FLOAT;
	}

	public function parse(array $parameters) : BaseResult{}
}
