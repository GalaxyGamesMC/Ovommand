<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\enum\SoftEnum;
use galaxygames\ovommand\parameter\type\ParameterTypes;

class SoftEnumParameter extends BaseParameter{
    $this->enum =

    public function __construct(string $name, SoftEnum|string $enum, bool $optional = false, int $flag = 0){
        $enumManager = EnumManager::getInstance();
        if ($enum instanceof SoftEnum) {
            $enum = $enum->getName();
        }
        $enumManager->getSoftEnum($enum);

        parent::__construct($name, $optional, $flag);
    }

    public function getNetworkType() : ParameterTypes{
        return ParameterTypes::ENUM;
    }
}

$this->addParameter(0, new SoftEnumParameter($name))
