<?php
declare(strict_types = 1);

namespace galaxygames\ovommand\constraint;

use galaxygames\ovommand\fetus\IParametable;
use pocketmine\command\CommandSender;

abstract class BaseConstraint{
	protected IParametable $context;

	/**
	 * BaseConstraint constructor.
	 *
	 * "Context" is required so that this new-constraint-system doesn't hinder getting command info
	 *
	 * @param IParametable $context
	 */
	public function __construct(IParametable $context){
		$this->context = $context;
	}

	public function getContext() : IParametable{
		return $this->context;
	}

	abstract public function test(CommandSender $sender, string $aliasUsed, array $args) : bool;

	abstract public function onFailure(CommandSender $sender, string $aliasUsed, array $args) : void;

	abstract public function isVisibleTo(CommandSender $sender) : bool;
}
