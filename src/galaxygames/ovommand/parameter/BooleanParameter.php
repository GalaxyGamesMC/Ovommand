<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\enum\HardEnum;

class BooleanParameter extends HardEnumParameter{
    protected HardEnum $enum;

    public function prepare() : void{
        $this->enum = EnumManager::getInstance()->getHardEnum(DefaultEnums::BOOLEAN);
    }
}
