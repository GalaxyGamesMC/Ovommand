<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\parse\Coordinates;
use galaxygames\ovommand\parameter\type\ParameterTypes;

class PositionParameter extends BaseParameter{
	public function getName() : string{
		return "x y z";
	}

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::POSITION;
	}

	public function canParse(string $in) : bool{}

	public function canParseArgs(array $args) : ?Coordinates{
		if (count($args) > $this->getSpanLength()) {
			throw new \InvalidArgumentException("Too many args");
		}
		$unmatch = "";
		$genType = null;
		$types = [];
		$values = [];
		foreach ($args as $i => $arg) {
			if (str_contains($arg, " ")) {
				$unmatch = $arg;
				break;
			}
			if (!preg_match("/^([~^]?[+-]?\d+(?:\.\d+)?)$/", $arg)) {
				$unmatch = $arg;
				break;
			}
			$type = match ($u = substr($arg, 0, 1)) {
				"~" => Coordinates::TYPE_RELATIVE,
				"^" => Coordinates::TYPE_LOCAL,
				default => Coordinates::TYPE_DEFAULT
			};
			if ($genType === null) {
				$genType = $type;
			}
			if ($type === Coordinates::TYPE_LOCAL && $genType !== Coordinates::TYPE_LOCAL) {
				$unmatch = $arg;
				break;
			}
			if ($genType === Coordinates::TYPE_LOCAL && $type !== Coordinates::TYPE_LOCAL) {
				$unmatch = $arg;
				break;
			}
			$value = ltrim($arg, $u);
			$nValue = str_contains($value, ".") ? (float) $value : (int) $value;
			$types[$i] = $type;
			$values[$i] = $nValue;
		}
		if ($unmatch !== "") {
			$syntax = implode(" ", $args);
			echo redmsg(dumpUnexpected($syntax, $unmatch)) . "\n";
			return null;
		}
		return Coordinates::fromData(...$values, ...$types);
	}

	public function parse(string $in) : mixed{}

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
