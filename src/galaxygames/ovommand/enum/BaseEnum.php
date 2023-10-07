<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\exception\ExceptionMessage;
use shared\galaxygames\ovommand\fetus\enum\OvoEnum;

abstract class BaseEnum extends OvoEnum{
	/**
	 * @param string                $name The name of the enum, E.g: [parameterName: enumName]
	 * @param array<string, mixed> $values The default values
	 * @param array<string, string|string[]> $showAliases The aliases for values, but they will show or have type hint ingame!
	 * @param array<string, string|string[]> $hiddenAliases The aliases for values, but they won't show or have type hint ingame!
	 */
	public function __construct(protected string $name, array $values = [], array $showAliases = [], array $hiddenAliases = []){
		$this->values = $values;
		$this->setAliases($showAliases);
		$this->setAliases($hiddenAliases, true);
	}

	/**
	 * @phpstan-param array<string, string|string[]> $aliases
	 */
	public function setAliases(array $aliases, bool $isHidden = false) : void{
		$isHidden ? $aliasesList = &$this->hiddenAliases : $aliasesList = &$this->showAliases;
		foreach ($aliases as $key => $alias) {
			if (!isset($this->values[$key])) {
				throw new EnumException(ExceptionMessage::MSG_ENUM_ALIAS_UNKNOWN_KEY->getErrorMessage(["aliasName" => $alias, "key" => $key]), EnumException::ENUM_ALIAS_UNKNOWN_KEY_ERROR);
			}
			if (is_string($alias)) {
				if (isset($this->showAliases[$alias]) || isset($this->hiddenAliases[$alias])) {
					throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["aliasName" => $alias]), EnumException::ENUM_ALIAS_REGISTERED_ERROR);
				}
				$aliasesList[$alias] = $key;
			} elseif (is_array($alias)) {
				foreach ($alias as $a) {
					if (!is_string($alias)) {
						throw new EnumException(ExceptionMessage::MSG_ENUM_ALIAS_UNKNOWN_TYPE->getErrorMessage(["aliasName" => (string) $a, "type" => gettype($a)]), EnumException::ENUM_ALIAS_UNKNOWN_TYPE_ERROR);
					}
					if (isset($this->showAliases[$alias]) || isset($this->hiddenAliases[$alias])) {
						throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["aliasName" => $alias]), EnumException::ENUM_ALIAS_REGISTERED_ERROR);
					}
					$aliasesList[$a] = $key;
				}
			} else {
				throw new EnumException(ExceptionMessage::MSG_ENUM_ALIAS_UNKNOWN_TYPE->getErrorMessage(["aliasName" => $alias, "type" => gettype($alias)]), EnumException::ENUM_ALIAS_UNKNOWN_TYPE_ERROR);
			}
		}
	}

	public function getValue(string $key) : mixed{
		$parentKey = $this->showAliases[$key] ?? $this->hiddenAliases[$key] ?? $key;
		return $this->values[$parentKey] ?? null; //What if null is bound with the key :c
	}

	public function hasValue(string $key) : bool{
		$parentKey = $this->showAliases[$key] ?? $this->hiddenAliases[$key] ?? $key;
		return isset($this->values[$parentKey]);
	}
}
