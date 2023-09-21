<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BaseResult;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;

class IntParameter extends BaseParameter{
	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::INT;
	}

	public function parse(array $parameters) : BaseResult{
		$f = implode("", $parameters);
		if (is_int((int) $f)) { //TODO: is_int($f) phpstorm bugs?
			return ValueResult::create($f);
		}
		return BrokenSyntaxResult::create("$f is not a int number!"); //TODO: better msg
	}

	public function getValueName() : string{
		return "int";
	}
}
