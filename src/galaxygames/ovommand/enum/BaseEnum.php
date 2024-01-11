<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\utils\MessageParser;
use shared\galaxygames\ovommand\fetus\enum\OvoEnum;

abstract class BaseEnum extends OvoEnum{
	/**
	 * @param string $name The name of the enum, E.g: [parameterName: enumName]
	 * @param array<string, mixed> $values The input values of an enum
	 * @param array<string, string|string[]> $showAliases The aliases for values, but they will show and have auto-hint ingame!
	 * @param array<string, string|string[]> $hiddenAliases The aliases for values, but they won't show or have auto-hint ingame!
	 */
	public function __construct(protected string $name, array $values = [], array $showAliases = [], array $hiddenAliases = [], protected bool $isReadonly = false, protected bool $isDefault = false){
		foreach ($values as $key => $value) {
			if (!is_string($key)) {
				throw new EnumException(MessageParser::EXCEPTION_ENUM_INVALID_VALUE_NAME_TYPE->value, EnumException::ENUM_INVALID_VALUE_NAME_TYPE); //TODO: Better msg
			}
			if ($value === null) {
				throw new EnumException(MessageParser::EXCEPTION_ENUM_NULL_VALUE->value, EnumException::ENUM_NULL_VALUE); //TODO: Better msg
			}
		}
		$this->values = $values;
		$this->addAliases($showAliases);
		$this->addAliases($hiddenAliases, true);
	}

	/** @param array<string, string|string[]> $aliases */
	public function addAliases(array $aliases, bool $isHidden = false) : void{
		$isHidden ? $aliasesList = &$this->hiddenAliases : $aliasesList = &$this->showAliases;
		foreach ($aliases as $key => $alias) {
			if (is_string($alias)) {
				if (!isset($this->values[$key])) {
					throw new EnumException(MessageParser::EXCEPTION_ENUM_ALIAS_UNKNOWN_KEY->translate(["aliasName" => $alias, "key" => $key]), EnumException::ENUM_ALIAS_UNKNOWN_KEY);
				}
				if (isset($this->showAliases[$alias]) || isset($this->hiddenAliases[$alias])) {
					throw new EnumException(MessageParser::EXCEPTION_ENUM_ALIAS_REGISTERED->translate(["aliasName" => $alias]), EnumException::ENUM_ALIAS_REGISTERED);
				}
				$aliasesList[$alias] = $key;
			} elseif (is_array($alias)) {
				foreach ($alias as $a) {
					if (!isset($this->values[$key])) {
						throw new EnumException(MessageParser::EXCEPTION_ENUM_ALIAS_UNKNOWN_KEY->translate(["aliasName" => $a, "key" => $key]), EnumException::ENUM_ALIAS_UNKNOWN_KEY);
					}
					if (!is_string($a)) {
						throw new EnumException(MessageParser::EXCEPTION_ENUM_ALIAS_UNKNOWN_TYPE->translate(["key" => $key, "type" => gettype($a)]), EnumException::ENUM_ALIAS_UNKNOWN_TYPE);
					}
					if (isset($this->showAliases[$a]) || isset($this->hiddenAliases[$a])) {
						throw new EnumException(MessageParser::EXCEPTION_ENUM_ALIAS_REGISTERED->translate(["aliasName" => $a]), EnumException::ENUM_ALIAS_REGISTERED);
					}
					$aliasesList[$a] = $key;
				}
			} else {
				throw new EnumException(MessageParser::EXCEPTION_ENUM_ALIAS_UNKNOWN_TYPE->translate(["key" => $key, "type" => gettype($alias)]), EnumException::ENUM_ALIAS_UNKNOWN_TYPE);
			}
		}
	}

	/** @param string[] $aliases */
	public function removeAliases(array $aliases, bool $isHidden = false) : void{
		$isHidden ? $aliasesList = &$this->hiddenAliases : $aliasesList = &$this->showAliases;
		foreach ($aliases as $alias) {
			unset($aliasesList[$alias]);
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
