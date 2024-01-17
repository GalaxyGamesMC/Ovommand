<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

interface IEnum{
	public function getName() : string;
	public function isDefault() : bool;
	public function encode() : CommandEnum;
	public function isSoft() : bool;
	public function getValue(string $key) : mixed;
	public function removeValue(string $key) : void;
	public function removeValuesBySpreading(string ...$keys) : void;
	/** @param string[] $context */
	public function removeValues(array $context) : void;
	public function getRawValues() : array;
	public function getHiddenAliases() : array;
	public function getShowAliases() : array;

	/** @param array<string, string|string[]> $aliases */
	public function addAliases(array $aliases, bool $isHidden = false) : void;
	/** @param string[] $aliases */
	public function removeAliases(array $aliases, bool $isHidden = false) : void;

	/**
	 * @param string|string[] $showAliases
	 * @param string|string[] $hiddenAliases
	 */
	public function addValue(string $value, mixed $bindValue = null, string|array $showAliases = [], string|array $hiddenAliases = []) : void;
	/**
	 * @param array<string, mixed> $context
	 * @param array<string, string|string[]> $showAliases
	 * @param array<string, string|string[]> $hiddenAliases
	 */
	public function addValues(array $context, array $showAliases = [], array $hiddenAliases = []) : void;

	public function changeValue(string $key, mixed $value) : void;
}
