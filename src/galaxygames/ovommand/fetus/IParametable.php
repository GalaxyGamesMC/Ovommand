<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\parameter\BaseParameter;
use pocketmine\command\CommandSender;

interface IParametable{
	public function generateUsageMessage() : string;

	public function hasParameters() : bool;

	/**
	 * @return BaseParameter[][]
	 */
	public function getParameterList() : array;

	public function parseParameters(array $rawArgs, CommandSender $sender) : array;

	public function registerParameter(int $position, BaseParameter $argument) : void;
}
