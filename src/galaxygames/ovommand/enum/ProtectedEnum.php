<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;

// This was made so users would know what went wrong.
class ProtectedEnum extends \shared\galaxygames\ovommand\fetus\enum\ProtectedEnum{
	public function removeValue(string $key) : void{
		throw new EnumException("Tried to remove values in a protected enum!", EnumException::ENUM_EDIT_PROTECTED_ENUM); //TODO: change msg
	}

	public function removeValuesBySpreading(string ...$keys) : void{
		throw new EnumException("Tried to remove values in a protected enum!", EnumException::ENUM_EDIT_PROTECTED_ENUM); //TODO: change msg
	}

	/** @param string[] $context */
	public function removeValues(array $context) : void{
		throw new EnumException("Tried to remove values in a protected enum!", EnumException::ENUM_EDIT_PROTECTED_ENUM); //TODO: change msg
	}

	/**
	 * @param string|string[] $showAliases
	 * @param string|string[] $hiddenAliases
	 */
	public function addValue(string $value, mixed $bindValue = null, array|string $showAliases = [], array|string $hiddenAliases = []) : void{
		throw new EnumException("Tried to add value in a protected enum!", EnumException::ENUM_EDIT_PROTECTED_ENUM); //TODO: change msg
	}

	/**
	 * @param array<string, mixed> $context
	 * @param array<string, string|string[]> $showAliases
	 * @param array<string, string|string[]> $hiddenAliases
	 */
	public function addValues(array $context, array $showAliases = [], array $hiddenAliases = []) : void{
		throw new EnumException("Tried to add values in a protected enum!", EnumException::ENUM_EDIT_PROTECTED_ENUM); //TODO: change msg
	}

	public function changeValue(string $key, mixed $value) : void{
		throw new EnumException("Tried to change value in a protected enum!", EnumException::ENUM_EDIT_PROTECTED_ENUM); //TODO: change msg
	}
}
