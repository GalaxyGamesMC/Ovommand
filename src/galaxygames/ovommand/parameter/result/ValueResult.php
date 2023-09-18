<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

use galaxygames\ovommand\parameter\result\BaseResult;

class ValueResult extends BaseResult{
	public function __construct(protected $value){}

	public static function create(mixed $value) : self{
		return new ValueResult($value);
	}

	public function getValue() : mixed{
		return $this->value;
	}
}
