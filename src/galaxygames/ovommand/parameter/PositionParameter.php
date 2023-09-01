<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\type\ParameterTypes;

class PositionParameter extends BaseParameter{
    public function getName() : string{
        return "x y z";
    }

    public function getNetworkType() : ParameterTypes{
        return ParameterTypes::POSITION;
    }

    public function canParse(string $in) : bool{
    }

    public function canParseArgs(array $args) : bool{
        $unmatch = "";
        foreach ($args as $arg) {
            if (str_contains($arg, " ")) {
                $unmatch = $arg;
                $out = false;
                break;
            }
            if (!preg_match("/^([~^]?[+-]?\d+(?:\.\d+)?)$/", $arg)) {
                $unmatch = $arg;
                break;
            }

        }
        if ($unmatch !== "") {
            $syntax = implode(" ", $args);
            $syntax = str_replace($unmatch, ">>$unmatch<<", $syntax);
            var_dump($syntax);
            return false;
        }
        return true;
    }

    public function parse(string $in) : mixed{

    }

    public function getSpanLength() : int{
        return 3;
    }
}
// only 1 para:  ^([~^]?[+-]?\d+(?:\.\d+)?)$   https://rubular.com/r/so1nkzsJzfGEBw  -> https://rubular.com/r/esNeY9PLjjkhnG
// all 3 para   ^([~^]?[+-]?\d+(?:\.\d+)?)\s([~^]?[+-]?\d+(?:\.\d+)?)\s([~^]?[+-]?\d+(?:\.\d+)?)$   https://rubular.com/r/QK1xa2lxrJilVj
// ([~|^]?[+-]?\d+(?:\.\d+)?)  https://rubular.com/r/zXZjbTLolo6LHn preg_match_all?, one group... no match bool return!
// (~|\^?)([+-]?\d+(?:\.\d+)?) https://rubular.com/r/ecvvQo5giX8fcx preg_match_all?, multiple group, no match bool return!


// Matched test!
/*
 https://3v4l.org/4JT7X

https://3v4l.org/H1lq6
 */



// ([~^]?)([+-]?[\d]?[\d.\d]+) https://rubular.com/r/6tkKRBfOX58PZz -> float type failed!
// ([~^]?)([+-]?)([\d]?[\d.\d]+)
// ([~^]?)([+-]?)(\d+)
// [+-]?([0-9]*[.]?[0-9]+)
