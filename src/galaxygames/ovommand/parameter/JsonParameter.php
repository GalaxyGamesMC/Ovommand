<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

class JsonParameter extends BaseParameter{
	public function getValueName() : string{ return "json"; }

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::JSON;
	}

	public function parse(array $parameters) : BaseResult{
		$pResult = parent::parse($parameters);
		if ($pResult instanceof BrokenSyntaxResult) {
			return $pResult;
		}
		$parameter = $parameters[0];
		try {
			$data = json_decode($parameter, false, 512, JSON_THROW_ON_ERROR);
			return ValueResult::create($data);
		} catch (\JsonException $e) {
			return BrokenSyntaxResult::create($e->getMessage(), $parameter);
		}
	}
}
