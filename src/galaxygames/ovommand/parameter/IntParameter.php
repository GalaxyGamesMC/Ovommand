<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\type\ParameterTypes;

class IntParameter extends BaseParameter{
	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::INT;
	}

	public function canParse(string $in) : bool{
		return (bool) preg_match("/^-?\d+$/", $in);
	}

	public function parse(string $in) : int{
		return (int) $in;
	}
}
