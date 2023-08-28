<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\type\ParameterTypes;

class PositionParameter extends BaseParameter{
    public function getNetworkType() : ParameterTypes{
        return ParameterTypes::POSITION;
    }

    public function has

    public function canParse(string $in) : bool{

    }

    public function parse(string $in) : mixed{}
}
