<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandOverload;

interface IOvommand{
	/** @return BaseConstraint[] */
	public function getConstraints() : array;
	/** @return CommandOverload[] */
	public function generateOverloads(CommandSender $sender) : array;
}
