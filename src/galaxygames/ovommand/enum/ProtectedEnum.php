<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\utils\MessageParser;

class ProtectedEnum extends \shared\galaxygames\ovommand\fetus\enum\ProtectedEnum{
	public function addAliases(array $aliases, bool $isHidden = false) : void{
		throw new EnumException(MessageParser::EXCEPTION_ENUM_ADD_PROTECTED_ALIAS->translate(['enumName' => $this->getName()]), EnumException::ENUM_EDIT_PROTECTED_ENUM);
	}

	public function removeAliases(array $aliases, bool $isHidden = false) : void{
		throw new EnumException(MessageParser::EXCEPTION_ENUM_REMOVE_PROTECTED_ALIAS->translate(['enumName' => $this->getName()]), EnumException::ENUM_EDIT_PROTECTED_ENUM);
	}

	public function removeValue(string ...$key) : void{
		$this->removeValues([]);
	}

	/** @param string[] $keys */
	public function removeValues(array $keys) : void{
		throw new EnumException(MessageParser::EXCEPTION_ENUM_REMOVE_PROTECTED_VALUE->translate(['enumName' => $this->getName()]), EnumException::ENUM_EDIT_PROTECTED_ENUM);
	}

	/**
	 * @param string|string[] $showAliases
	 * @param string|string[] $hiddenAliases
	 */
	public function addValue(string $value, mixed $bindValue = null, array|string $showAliases = [], array|string $hiddenAliases = []) : void{
		$this->addValues([]);
	}

	/**
	 * @param array<string, mixed> $values
	 * @param array<string, string|string[]> $showAliases
	 * @param array<string, string|string[]> $hiddenAliases
	 */
	public function addValues(array $values, array $showAliases = [], array $hiddenAliases = []) : void{
		throw new EnumException(MessageParser::EXCEPTION_ENUM_ADD_PROTECTED_VALUE->translate(['enumName' => $this->getName()]), EnumException::ENUM_EDIT_PROTECTED_ENUM);
	}

	public function changeValue(string $key, mixed $value) : void{
		throw new EnumException(MessageParser::EXCEPTION_ENUM_REMOVE_PROTECTED_VALUE->translate(['key' => $key, 'enumName' => $this->getName()]), EnumException::ENUM_EDIT_PROTECTED_ENUM);
	}
}
