<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\BaseEnum;
use galaxygames\ovommand\enum\DefaultEnums;

class BooleanParameter extends EnumParameter{
	protected BaseEnum $enum;

	public function __construct(string $name, bool $optional = false, int $flag = 0){
		parent::__construct($name, DefaultEnums::BOOLEAN, $optional, $flag);
	}
}
