<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\type\ParameterTypes;

class PositionParameter extends BaseParameter{
    public function getNetworkType() : ParameterTypes{
        return ParameterTypes::POSITION;
    }

    public function canParse(string $in) : bool{

    }

    public function parse(string $in) : mixed{}
}
// ([~^]?)([+-]?[\d]?[\d.\d]+) https://rubular.com/r/6tkKRBfOX58PZz

// ([~^]?)([+-]?)([\d]?[\d.\d]+)

// ([~^]?)([+-]?)(\d+)

// [+-]?([0-9]*[.]?[0-9]+)
