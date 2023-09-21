<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\BaseEnum;
use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;

class BooleanParameter extends EnumParameter{
	protected BaseEnum $enum;

	public function __construct(string $name, bool $optional = false, int $flag = 0){
		parent::__construct($name, EnumManager::getInstance()->getHardEnum(DefaultEnums::BOOLEAN), $optional, $flag);
	}
}
