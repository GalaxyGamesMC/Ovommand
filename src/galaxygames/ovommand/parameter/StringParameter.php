<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

class StringParameter extends BaseParameter{
	public function getValueName() : string{ return "string"; }

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::STRING;
	}

	public function parse(array $parameters) : BaseResult{
		parent::parse($parameters);

		$f = implode("", $parameters);
		if (empty($f)) {
			return BrokenSyntaxResult::create($f);
		}
		return ValueResult::create($f);
	}
}
