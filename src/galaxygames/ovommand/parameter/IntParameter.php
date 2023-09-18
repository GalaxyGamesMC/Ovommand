<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\ValueResult;
use galaxygames\ovommand\parameter\type\ParameterTypes;

class IntParameter extends BaseParameter{
	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::INT;
	}

	public function parse(array $parameters) : ValueResult{
		return ValueResult::create($parameters);
	}
}
