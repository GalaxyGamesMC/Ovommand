<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

class HardEnum extends BaseEnum{

    public function encode() : CommandEnum{
        return new CommandEnum($this->name, [...$this->values, ...array_keys($this->showAliases)]);
    }
}
