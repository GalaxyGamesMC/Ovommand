<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

abstract class BaseEnum{
	protected array $values;
	protected array $hiddenAliases = [];
	protected array $showAliases = [];

	// huh, <parameterName: enumName> doesn't show up...

	/**
	 * @param string $name The name of the enum, E.g. <parameterName: enumName>
	 * @param array  $values The default values
	 * @param array  $hiddenAliases The aliases for values, but they won't show or have type hint ingame!
	 * @param array  $showAliases The aliases for values, but they will show or have type hint ingame!
	 */
	public function __construct(protected string $name, array $values = [], array $showAliases = [], array $hiddenAliases = []){
		$this->values = $values;
//		$this->values = Utils::collapseBindingEnumInputs($values);
		$this->setAliases($showAliases);
		$this->setAliases($hiddenAliases, true);
	}

	final public function getName() : string{
		return $this->name;
	}

	/**
	 * @param string[] $aliases
	 * @param bool     $isHidden
	 * @phpstan-param array<string, string|array<int|string> $aliases
	 *
	 * @return void
	 */
	public function setAliases(array $aliases, bool $isHidden = false) : void{
		//		if ($isHidden) {
		//			$aliasesList = &$this->hiddenAliases;
		//		} else {
		//			$aliasesList = &$this->showAliases;
		//		}
		//		$aliasesList = $isHidden ? $aliasesList = &$this->showAliases : $aliasesList = &$this->hiddenAliases; lol
		$isHidden ? $aliasesList = &$this->showAliases : $aliasesList = &$this->hiddenAliases;
		// is this slower than using $this->hiddenAliases; and $this->showAliases itself?

		foreach ($aliases as $key => $alias) {
			if (isset($this->showAliases[$alias]) || isset($this->hiddenAliases[$alias])) {
				throw new \RuntimeException("Alias already used for a key!");
			}
			if (!isset($this->values[$key])) {
				throw new \RuntimeException("Unknown key!");
			}
			if (is_array($alias)) {
				foreach ($alias as $a) {
					$aliasesList[$a] = $key;
				}
				//				$this->hiddenAliases[$alias] = array_unique($alias); //TODO: non string alias!
			} elseif (is_int($key) || is_string($key)) {
				$aliasesList[$alias] = $key;
			} else {
				throw new \RuntimeException("Unknown alias type!");
			}
		}
	}

	abstract public function encode() : CommandEnum;
	abstract public function isSoft() : bool;

	public function getRawValues() : array{
		return $this->values;
	}

	public function getValue(string $key) : mixed{
		$parentKey = $this->showAliases[$key] ?? $this->hiddenAliases[$key] ?? $key;
		return $this->values[$parentKey] ?? null; //TODO: What if null is bound with the key :c
	}

	public function hasValue(string $key) : bool{
		$parentKey = $this->showAliases[$key] ?? $this->hiddenAliases[$key] ?? $key;
		return isset($this->values[$parentKey]);
	}
}
