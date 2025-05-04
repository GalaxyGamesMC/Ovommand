<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\utils\MessageParser;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\Server;
use shared\galaxygames\ovommand\fetus\enum\IStaticEnum;

class HardEnum extends BaseEnum implements IStaticEnum{
	public function encode() : CommandEnum{
		return new CommandEnum($this->name, [...array_keys($this->values), ...array_keys($this->visibleAliases)]);
	}

	public function addAliases(array $aliases, bool $isHidden = false) : void{
		if (Server::getInstance()->getTick() !== 0) { // hacky method for checking if the server has started
			throw new EnumException(MessageParser::EXCEPTION_ENUM_RUNNING_HARD_ENUM_ADD_ALIAS->translate(['enumName' => $this->getName()]), EnumException::ENUM_EDIT_RUNNING_HARD_ENUM);
		}
		parent::addAliases($aliases, $isHidden);
	}

	public function removeAliases(array $aliases, bool $isHidden = false) : void{
		if (Server::getInstance()->getTick() !== 0) { // hacky method for checking if the server has started
			throw new EnumException(MessageParser::EXCEPTION_ENUM_RUNNING_HARD_ENUM_REMOVE_ALIAS->translate(['enumName' => $this->getName()]), EnumException::ENUM_EDIT_RUNNING_HARD_ENUM);
		}
		parent::removeAliases( $aliases, $isHidden);
	}

	public function removeValue(string ...$key) : void{ $this->removeValues($key); }

	public function removeValues(array $keys) : void{
		if (Server::getInstance()->getTick() !== 0) {
			throw new EnumException(MessageParser::EXCEPTION_ENUM_RUNNING_HARD_ENUM_REMOVE_VALUE->translate(['enumName' => $this->getName()]), EnumException::ENUM_EDIT_RUNNING_HARD_ENUM);
		}
		$updates = [];
		foreach ($keys as $k) {
			if (isset($this->values[$k])) {
				unset($this->values[$k]);
				$updates[] = $k;
			}
		}
		if (count($updates) !== 0) {
			$this->hiddenAliases = array_diff($this->hiddenAliases, $updates);
			$this->visibleAliases = array_diff($this->visibleAliases, $updates);
		}
	}

	public function addValue(string $value, mixed $bindValue = null, array|string $showAliases = [], array|string $hiddenAliases = []) : void{
		$this->addValues([$value => $bindValue], [$value => $showAliases], [$value => $hiddenAliases]);
	}

	public function addValues(array $values, array $showAliases = [], array $hiddenAliases = []) : void{
		if (Server::getInstance()->getTick() !== 0) {
			throw new EnumException(MessageParser::EXCEPTION_ENUM_RUNNING_HARD_ENUM_ADD_VALUE->translate(['enumName' => $this->getName()]), EnumException::ENUM_EDIT_RUNNING_HARD_ENUM);
		}
		$updates = [];
		foreach ($values as $k => $v) {
			if (!isset($this->values[$k])) {
				$this->values[$k] = $v;
				$updates[] = $k;
			}
		}
		if (!empty($updates)) {
			$this->addAliases($showAliases);
			$this->addAliases($hiddenAliases, true);
		}
	}

	public function changeValue(string $key, mixed $value) : void{
		if (Server::getInstance()->getTick() !== 0) {
			throw new EnumException(MessageParser::EXCEPTION_ENUM_RUNNING_HARD_ENUM_CHANGE_VALUE->translate(['key' => $key, 'enumName' => $this->getName()]), EnumException::ENUM_EDIT_RUNNING_HARD_ENUM);
		}
		if (isset($this->values[$key])) {
			$this->values[$key] = $value;
		}
	}
}
