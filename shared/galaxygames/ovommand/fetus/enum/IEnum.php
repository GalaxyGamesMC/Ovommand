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

	public function addValue(string $value, mixed $bindValue = null, string|array $showAliases = [], string|array $hiddenAliases = []) : void;
	public function addValues(array $context, array $showAliases = [], array $hiddenAliases = []) : void;
	public function changeValue(string $key, mixed $value) : void;
}
