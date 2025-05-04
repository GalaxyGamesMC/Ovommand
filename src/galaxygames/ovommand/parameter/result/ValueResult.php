<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

class ValueResult extends BaseResult implements \shared\galaxygames\ovommand\fetus\result\ISucceedResult{
	public function __construct(protected mixed $value){}

	public static function create(mixed $value) : self{
		return new ValueResult($value);
	}

	public function getValue() : mixed{
		return $this->value;
	}
}
