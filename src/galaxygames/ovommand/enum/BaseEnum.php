<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\exception\ExceptionMessage;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use shared\galaxygames\ovommand\fetus\IEnum;

abstract class BaseEnum implements IEnum{
	/** @var array<string, mixed> $values */
	protected array $values;
	/** @var string[] $hiddenAliases */
	protected array $hiddenAliases = [];
	/** @var string[] $showAliases */
	protected array $showAliases = [];

	/**
	 * @param string                $name The name of the enum, E.g: [parameterName: enumName]
	 * @param array<string, mixed> $values The default values
	 * @param array<string, string|string[]> $showAliases The aliases for values, but they will show or have type hint ingame!
	 * @param array<string, string|string[]> $hiddenAliases The aliases for values, but they won't show or have type hint ingame!
	 */
	public function __construct(protected string $name, array $values = [], array $showAliases = [], array $hiddenAliases = []){
		$this->values = $values; //TODO: validate inputs
//		$this->values = Utils::collapseBindingEnumInputs($values);
		$this->setAliases($showAliases);
		$this->setAliases($hiddenAliases, true);
	}

	final public function getName() : string{
		return $this->name;
	}

	/**
	 * @phpstan-param array<string, string|string[]> $aliases
	 */
	public function setAliases(array $aliases, bool $isHidden = false) : void{
		//		if ($isHidden) {
		//			$aliasesList = &$this->hiddenAliases;
		//		} else {
		//			$aliasesList = &$this->showAliases;
		//		}
		$isHidden ? $aliasesList = &$this->hiddenAliases : $aliasesList = &$this->showAliases;
		// is this slower than using $this->hiddenAliases; and $this->showAliases itself?

		foreach ($aliases as $key => $alias) {
			if (is_string($alias)) {
				if (!isset($this->values[$key])) {
					throw new EnumException(ExceptionMessage::MSG_ENUM_ALIAS_UNKNOWN_KEY->getErrorMessage(["aliasName" => $alias, "key" => $key]), EnumException::ENUM_ALIAS_UNKNOWN_KEY_ERROR);
				}
				if (isset($this->showAliases[$alias]) || isset($this->hiddenAliases[$alias])) {
					throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["aliasName" => $alias]), EnumException::ENUM_ALIAS_REGISTERED_ERROR);
				}
				$aliasesList[$alias] = $key;
			} elseif (is_array($alias)) {
				foreach ($alias as $a) {
					if (!isset($this->values[$key])) {
						throw new EnumException(ExceptionMessage::MSG_ENUM_ALIAS_UNKNOWN_KEY->getErrorMessage(["aliasName" => $a, "key" => $key]), EnumException::ENUM_ALIAS_UNKNOWN_KEY_ERROR);
					}
					if (!is_string($a)) {
						throw new EnumException(ExceptionMessage::MSG_ENUM_ALIAS_UNKNOWN_TYPE->getErrorMessage(["aliasName" => (string) $a, "type" => gettype($a)]), EnumException::ENUM_ALIAS_UNKNOWN_TYPE_ERROR);
					}
					if (isset($this->showAliases[$a]) || isset($this->hiddenAliases[$a])) {
						throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["aliasName" => $a]), EnumException::ENUM_ALIAS_REGISTERED_ERROR);
					}
					$aliasesList[$a] = $key;
				}
			} else {
				throw new EnumException(ExceptionMessage::MSG_ENUM_ALIAS_UNKNOWN_TYPE->getErrorMessage(["aliasName" => $alias, "type" => gettype($alias)]), EnumException::ENUM_ALIAS_UNKNOWN_TYPE_ERROR);
			}
//			match (true) {
//				!isset($this->values[$key]) => throw new EnumException(ExceptionMessage::MSG_ENUM_ALIAS_UNKNOWN_KEY->getErrorMessage(["aliasName" => (string)$alias, "key" => $key]), EnumException::ENUM_ALIAS_UNKNOWN_KEY_ERROR),
//				isset($this->showAliases[$alias]) || isset($this->hiddenAliases[$alias]) => throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["aliasName" => (string)$alias]), EnumException::ENUM_ALIAS_REGISTERED_ERROR),
//				is_string($alias) => $aliasesList[$alias] = $key,
//				is_array($alias) => $aliasesList += array_fill_keys($alias, $key),
//				default => throw new EnumException(ExceptionMessage::MSG_ENUM_ALIAS_UNKNOWN_TYPE->getErrorMessage(["aliasName" => (string)$alias, "type" => gettype($alias)]), EnumException::ENUM_ALIAS_UNKNOWN_TYPE_ERROR)
//			};
		}
	}

	abstract public function encode() : CommandEnum;

	/**
	 * @return array<string, mixed>
	 */
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

	/**
	 * @return string[]
	 */
	public function getHiddenAliases() : array{
		return $this->hiddenAliases;
	}

	/**
	 * @return string[]
	 */
	public function getShowAliases() : array{
		return $this->showAliases;
	}
}
