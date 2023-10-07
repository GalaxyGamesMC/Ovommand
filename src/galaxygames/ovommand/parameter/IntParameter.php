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
		$f = implode("", $parameters);
		if (preg_match("/^\d+$/", $f)) {
			return ValueResult::create((int) $f);
		}
		return BrokenSyntaxResult::create($f); //TODO: better msg
	}

	public function getValueName() : string{
		return "int";
	}
}
