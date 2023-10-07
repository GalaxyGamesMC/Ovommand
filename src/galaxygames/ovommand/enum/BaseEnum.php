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
	public function __construct(protected string $name, array $values = [], array $showAliases = [], array $hiddenAliases = [], protected bool $isDefault = false){
		$this->values = $values;
		$this->setAliases($showAliases);
		$this->setAliases($hiddenAliases, true);
	}

	/**
	 * @param array<string, string|string[]> $aliases
	 */
	public function setAliases(array $aliases, bool $isHidden = false) : void{
		$isHidden ? $aliasesList = &$this->hiddenAliases : $aliasesList = &$this->showAliases;
		foreach ($aliases as $key => $alias) {
			if (is_string($alias)) {
				if (!isset($this->values[$key])) {
					throw new EnumException(ExceptionMessage::ENUM_ALIAS_UNKNOWN_KEY->getErrorMessage(["aliasName" => $alias, "key" => $key]), EnumException::ENUM_ALIAS_UNKNOWN_KEY);
				}
				if (isset($this->showAliases[$alias]) || isset($this->hiddenAliases[$alias])) {
					throw new EnumException(ExceptionMessage::ENUM_ALIAS_REGISTERED->getErrorMessage(["aliasName" => $alias]), EnumException::ENUM_ALIAS_REGISTERED);
				}
				$aliasesList[$alias] = $key;
			} elseif (is_array($alias)) {
				foreach ($alias as $a) {
					if (!isset($this->values[$key])) {
						throw new EnumException(ExceptionMessage::ENUM_ALIAS_UNKNOWN_KEY->getErrorMessage(["aliasName" => $a, "key" => $key]), EnumException::ENUM_ALIAS_UNKNOWN_KEY);
					}
					if (!is_string($a)) {
						throw new EnumException(ExceptionMessage::ENUM_ALIAS_UNKNOWN_TYPE->getErrorMessage(["key" => $key, "type" => gettype($a)]), EnumException::ENUM_ALIAS_UNKNOWN_TYPE);
					}
					if (isset($this->showAliases[$a]) || isset($this->hiddenAliases[$a])) {
						throw new EnumException(ExceptionMessage::ENUM_ALIAS_REGISTERED->getErrorMessage(["aliasName" => $a]), EnumException::ENUM_ALIAS_REGISTERED);
					}
					$aliasesList[$a] = $key;
				}
			} else {
				throw new EnumException(ExceptionMessage::ENUM_ALIAS_UNKNOWN_TYPE->getErrorMessage(["key" => $key, "type" => gettype($alias)]), EnumException::ENUM_ALIAS_UNKNOWN_TYPE);
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
