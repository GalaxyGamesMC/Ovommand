<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

interface IEnum{
	public function getName() : string;
	public function isDefault() : bool;
	public function encode() : CommandEnum;
	public function getValue(string $key) : mixed;
	public function isSoft() : bool;
	public function getRawValues() : array;
	public function getHiddenAliases() : array;
	public function getShowAliases() : array;
}
