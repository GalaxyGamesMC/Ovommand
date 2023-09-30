<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\result\BaseResult;
use pocketmine\command\CommandSender;

interface IParametable{
	/**
	 * @return BaseParameter[][]
	 */
	public function getOverloads() : array;

	public function hasOverloads() : bool;

	/**
	 * @param string[] $rawParams
	 *
	 * @return BaseResult[]
	 */
	public function parseParameters(array $rawParams) : array;

	public function registerParameter(int $overloadId, BaseParameter $parameter) : void;
}
