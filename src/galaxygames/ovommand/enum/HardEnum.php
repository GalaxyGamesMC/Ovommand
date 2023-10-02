<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use shared\galaxygames\ovommand\enum\fetus\IStaticEnum;

class HardEnum extends BaseEnum implements IStaticEnum{
	public function encode() : CommandEnum{
		return new CommandEnum($this->name, [...array_keys($this->values), ...array_keys($this->showAliases)]);
	}
}
