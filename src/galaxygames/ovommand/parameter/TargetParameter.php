<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\type\ParameterTypes;

class TargetParameter extends BaseParameter{
	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::TARGET;
	}


}
