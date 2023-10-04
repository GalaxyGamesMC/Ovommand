<?php
declare(strict_types=1);

namespace galaxygames\ovommand\constraint;

use galaxygames\ovommand\fetus\IParametable;
use pocketmine\command\CommandSender;
use shared\galaxygames\ovommand\fetus\IOvommand;

abstract class BaseConstraint{
	protected IOvommand $context;

	/**
	 * BaseConstraint constructor.
	 *
	 * "Context" is required so that this new-constraint-system doesn't hinder getting command info
	 *
	 * @param IOvommand $context
	 */
	public function __construct(IOvommand $context){
		$this->context = $context;
	}

	public function getContext() : IOvommand{
		return $this->context;
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
