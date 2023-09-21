<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\BaseCommand;
use galaxygames\ovommand\BaseSubCommand;
use galaxygames\ovommand\constraint\BaseConstraint;
use InvalidArgumentException;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

trait OvommandTrait{
	/** @var BaseConstraint[] */
	protected array $constraints;
	/** @var BaseCommand|BaseSubCommand */
	protected BaseSubCommand|BaseCommand $parent;
	/** @var BaseSubCommand[] $subCommands */
	protected array $subCommands = [];
//	protected array $subCommandAliases = []; //todo: good?
	protected CommandSender $currentSender;
	protected Translatable|string $usageMessage;

	public function registerSubCommand(BaseSubCommand $subCommand) : void{
		if (!isset($this->subCommands[$subName = $subCommand->getName()])) {
			$this->subCommands[$subName] = $subCommand->setParent($this);
			$aliases = [...$subCommand->getShowAliases(), ...$subCommand->getHiddenAliases()];
			foreach ($aliases as $alias) {
				if (!isset($this->subCommands[$alias])) {
					$this->subCommands[$alias] = $subCommand;
				} else {
					throw new InvalidArgumentException("SubCommand with same alias for '$alias' already exists");
				}
			}
		} else {
			throw new InvalidArgumentException("SubCommand with same name for '$subName' already exists");
		}
	}

	public function setUsageMessage(Translatable|string $translatable) : void{
		$this->usageMessage = $translatable;
	}

	abstract public function prepare(CommandSender $sender, string $aliasUsed, array $args) : void;
	abstract public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void;

	public function getParent() : BaseCommand|BaseSubCommand{
		return $this->parent;
	}

	public function setParent(BaseSubCommand|BaseCommand $parent) : self{
		$this->parent = $parent;
		return $this;
	}

	/**
	 * @param CommandSender $currentSender
	 *
	 * @internal Used to pass the current sender from the parent command
	 */
	public function setCurrentSender(CommandSender $currentSender) : void{
		$this->currentSender = $currentSender;
	}

	public function getCurrentSender() : CommandSender{
		return $this->currentSender;
	}

	public function addConstraint(BaseConstraint $constraint) : void{
		$this->constraints[] = $constraint;
	}

	/**
	 * @return BaseConstraint[]
	 */
	public function getConstraints() : array{
		return $this->constraints;
	}
}
