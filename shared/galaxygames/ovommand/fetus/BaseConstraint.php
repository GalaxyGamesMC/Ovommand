<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus;

use pocketmine\command\CommandSender;

abstract class BaseConstraint{
	protected IOvommand $ovommand;

	public function __construct(IOvommand $ovommand){
		$this->ovommand = $ovommand;
	}

	public function getOvommand() : IOvommand{
		return $this->ovommand;
	}

	/**
	 * @param string[] $args
	 */
	abstract public function test(CommandSender $sender, string $aliasUsed, array $args) : bool;

	/**
	 * @param string[] $args
	 */
	abstract public function onFailure(CommandSender $sender, string $aliasUsed, array $args) : void;

	abstract public function isVisibleTo(CommandSender $sender) : bool;
}
