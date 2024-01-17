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

	public function removeValue(string $key) : void{
		$this->removeValues([$key]);
	}

	public function removeValuesBySpreading(string ...$keys) : void{
		$this->removeValues($keys);
	}

	public function removeValues(array $context) : void{
		if (Server::getInstance()->getTick() !== 0) {
			throw new EnumException("Tried to remove values from a running hard enum!", EnumException::ENUM_EDIT_RUNNING_HARD_ENUM); //TODO: change msg
		}
		$updates = [];
		foreach ($context as $k) {
			if (isset($this->values[$k])) {
				unset($this->values[$k]);
				$updates[] = $k;
			}
		}
		if (count($updates) !== 0) {
			$this->hiddenAliases = array_diff($this->hiddenAliases, $updates);
			$this->showAliases = array_diff($this->showAliases, $updates);
		}
	}

	public function addValue(string $value, mixed $bindValue = null, array|string $showAliases = [], array|string $hiddenAliases = []) : void{
		$this->addValues([$value => $bindValue], [$value => $showAliases], [$value => $hiddenAliases]);
	}

	public function addValues(array $context, array $showAliases = [], array $hiddenAliases = []) : void{
		if (Server::getInstance()->getTick() !== 0) {
			throw new EnumException("Tried to add values from a running hard enum!", EnumException::ENUM_EDIT_RUNNING_HARD_ENUM); //TODO: change msg
		}
		$updates = [];
		foreach ($context as $k => $v) {
			if (!isset($this->values[$k])) {
				$this->values[$k] = $v;
				$updates[] = $k;
			}
		}
		if (count($updates) !== 0) {
			$this->addAliases($showAliases);
			$this->addAliases($hiddenAliases, true);
		}
	}

	public function changeValue(string $key, mixed $value) : void{
		if (Server::getInstance()->getTick() !== 0) {
			throw new EnumException("Tried to change value from a running hard enum!", EnumException::ENUM_EDIT_RUNNING_HARD_ENUM); //TODO: change msg
		}
		if (isset($this->values[$key])) {
			$this->values[$key] = $value;
		}
	}
}
