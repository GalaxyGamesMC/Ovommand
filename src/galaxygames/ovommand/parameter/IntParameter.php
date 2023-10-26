<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

class IntParameter extends BaseParameter{
	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::INT;
	}

	public function parse(array $parameters) : BaseResult{
		$i = implode("", $parameters);
		if (preg_match("/^\d+$/", $i)) {
			return ValueResult::create((int) $i);
		}
		return BrokenSyntaxResult::create($i);
	}

	public function getValueName() : string{ return "int"; }
}
