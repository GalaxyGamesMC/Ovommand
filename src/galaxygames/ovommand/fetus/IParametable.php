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

	public function parseParameters(array $rawParams, CommandSender $sender, string $commandLabel) : array;

	public function registerParameter(int $overloadId, BaseParameter $parameter) : void;
}
