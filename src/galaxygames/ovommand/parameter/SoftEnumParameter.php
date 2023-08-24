<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\type\ParameterTypes;

class SoftEnumParameter extends BaseParameter{
    public function getNetworkType() : ParameterTypes{
        return ParameterTypes::ENUM;
    }


}
