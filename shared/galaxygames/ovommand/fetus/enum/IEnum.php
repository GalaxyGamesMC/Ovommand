<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

interface IEnum{

	public function getName() : string;
	public function encode() : CommandEnum;
	public function getValue(string $key);
	public function getRawValues();
}
