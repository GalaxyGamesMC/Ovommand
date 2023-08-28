<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\enum\HardEnum;
use galaxygames\ovommand\enum\SoftEnum;
use galaxygames\ovommand\parameter\type\ParameterTypes;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

class HardEnumParameter extends BaseParameter{
    protected HardEnum $enum;

    public function __construct(string $name, HardEnum|string $enum, bool $optional = false, int $flag = 0){
        $enumManager = EnumManager::getInstance();
        if ($enum instanceof HardEnum) {
            $enum = $enum->getName();
        }
        $enum = $enumManager->getHardEnum($enum);
        if ($enum === null) {
            throw new \RuntimeException("Enum is not valid or not registered in Enum Manager"); //TODO: better msg
        }
        $this->enum = $enum;
        parent::__construct($name, $optional, $flag);
    }

    public function getNetworkType() : ParameterTypes{
        return ParameterTypes::ENUM;
    }

    public function encodeEnum() : CommandEnum{
        return $this->enum->encode();
    }
}
