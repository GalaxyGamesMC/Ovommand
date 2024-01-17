<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\Server;
use shared\galaxygames\ovommand\fetus\enum\IStaticEnum;

class HardEnum extends BaseEnum implements IStaticEnum{
	public function encode() : CommandEnum{
		return new CommandEnum($this->name, [...array_keys($this->values), ...array_keys($this->showAliases)]);
	}

	public function addAliases(array $aliases, bool $isHidden = false) : void{
		if (Server::getInstance()->getTick() !== 0) {
			throw new EnumException("Tried to add aliases to a running hard enum!", EnumException::ENUM_EDIT_RUNNING_HARD_ENUM); //TODO: change msg
		}
		parent::addAliases($aliases, $isHidden);
	}

	public function removeAliases(array $aliases, bool $isHidden = false) : void{
		if (Server::getInstance()->getTick() !== 0) {
			throw new EnumException("Tried to remove aliases from a running hard enum!", EnumException::ENUM_EDIT_RUNNING_HARD_ENUM); //TODO: change msg
		}
		parent::removeAliases( $aliases, $isHidden);
	}
}
