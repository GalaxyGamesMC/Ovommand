<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\enum\HardEnum;

class BooleanParameter extends HardEnumParameter{
    protected HardEnum $enum;

    public function __construct(string $name, bool $optional = false, int $flag = 0){
        parent::__construct($name, EnumManager::getInstance()->getHardEnum(DefaultEnums::BOOLEAN), $optional, $flag);
    }

    public function parse(string $in) : mixed{}
}
