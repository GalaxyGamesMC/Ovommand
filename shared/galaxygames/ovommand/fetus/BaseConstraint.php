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

	/** @param string[] $args */
	abstract public function constraint(CommandSender $sender, string $label, array $args) : bool;
	/** @param string[] $args */
	abstract public function onSuccess(CommandSender $sender, string $label, array $args) : void;
	/** @param string[] $args */
	abstract public function onFailure(CommandSender $sender, string $label, array $args) : void;

	public function setOvommand(IOvommand $ovommand) : void{
		$this->ovommand = $ovommand;
	}
}
