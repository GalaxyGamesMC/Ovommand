<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\enum\HardEnum;
use galaxygames\ovommand\parameter\type\ParameterTypes;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

class HardEnumParameter extends BaseParameter{
    protected HardEnum $enum;

    public function __construct(string $name, bool $optional = false, int $flag = 0){
        parent::__construct($name, $optional, $flag);

        $this->prepare();
    }

    public function prepare() : void{
        // COOP
    }

    public function getNetworkType() : ParameterTypes{
        return ParameterTypes::ENUM;
    }

    public function encodeEnum() : ?CommandEnum{
        return $this->enum->encode();
    }
}
