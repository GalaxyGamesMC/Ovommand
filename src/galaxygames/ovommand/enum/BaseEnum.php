<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\utils\Utils;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

abstract class BaseEnum{
	protected array $values;
	protected array $hiddenAliases;
	protected array $showAliases;

	// huh, <parameterName: enumName> doesn't show up...
	/**
	 * @param string $name The name of the enum, eg: <parameterName: enumName>
	 * @param array  $values The default values
	 * @param array  $hiddenAliases The aliases for values, but they won't show or have type hint ingame!
	 * @param array  $showAliases The aliases for values, but they will show or have type hint ingame!
	 */
	public function __construct(protected string $name, array $values = [], array $hiddenAliases = [], array $showAliases = []){
		$this->values = Utils::collapseBindingEnumInputs($values);
		$this->setHiddenAliases($hiddenAliases);
		$this->setShowAliases($showAliases);
	}

	final public function getName() : string{
		return $this->name;
	}

	// TODO: merge or reduce these 2 following functions, change method from "public" to something else?
	public function setHiddenAliases(array $aliases) : void{
		foreach ($aliases as $key => $alias) {
			if (!isset($this->values[$key])) {
				throw new \RuntimeException("Unknown key!");
			}
			if (is_array($alias)) {
				$this->hiddenAliases[$key] = array_unique($alias); //TODO: non string alias!
			} elseif (is_int($key) || is_string($key)) {
				$this->hiddenAliases[$key] = $alias;
			} else {
				throw new \RuntimeException("Unknown alias type!");
			}
		}
	}

	public function setShowAliases(array $aliases) : void{
		foreach ($aliases as $key => $alias) {
			if (!isset($this->values[$key])) {
				throw new \RuntimeException("Unknown key!");
			}
			if (is_array($alias)) {
				$this->showAliases[$key] = array_unique($alias); //TODO: non string alias!
			} elseif (is_int($key) || is_string($key)) {
				$this->showAliases[$key] = $alias;
			} else {
				throw new \RuntimeException("Unknown alias type!");
			}
		}
	}

	abstract public function encode() : CommandEnum;

	public function getRawValues() : array{
		return $this->values;
	}

	public function getValue(string $key) : mixed{
		$parentKey = $this->showAliases[$key] ?? $this->hiddenAliases ?? $key;
		return $this->values[$parentKey] ?? null; //TODO: What if null is bound with the key :c
	}

	public function hasValue(string $key) : bool{
		$parentKey = $this->showAliases[$key] ?? $this->hiddenAliases ?? $key;
		return isset($this->values[$parentKey]);
	}
}
