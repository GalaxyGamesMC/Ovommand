<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

class HardEnum{
    use CommandEnumTrait;

	public function encode() : CommandEnum{
		return new CommandEnum($this->name, $this->values);
	}
}
