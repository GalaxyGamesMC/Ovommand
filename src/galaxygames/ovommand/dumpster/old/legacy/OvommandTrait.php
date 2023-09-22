<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus\legacy;

use galaxygames\ovommand\constraint\BaseConstraint;
use InvalidArgumentException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

trait OvommandTrait{
	/** @var BaseConstraint[] */
	protected array $constraints;
	/** @var BaseCommand|BaseSubCommand */
	protected BaseSubCommand|BaseCommand $parent;
	/** @var BaseSubCommand[] $subCommands */
	protected array $subCommands = [];
	//	protected array $subCommandAliases = []; //todo: good?
	protected CommandSender $currentSender;

	final public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if (!$this->testPermission($sender)) {
			return;
		}
		$this->setCurrentSender($sender);
		if (count($args) > 0) {
			if (isset($this->subCommands[$label = $args[0]])) {
				array_shift($args);
				$execute = $this->subCommands[$label];
				$execute->setCurrentSender($sender);
				if (!$execute->testPermissionSilent($sender)) {
					$msg = $this->getPermissionMessage();
					if ($msg === null) {
						$sender->sendMessage($sender->getServer()->getLanguage()->translateString(TextFormat::RED . "%commands.generic.permission"));
					} elseif (empty($msg)) {
						$sender->sendMessage(str_replace("<permission>", $execute->getPermissions()[0], $msg));
					}
					return;
				}
				$execute->execute($sender, $label, $args);
			}
		} else {
			$passArgs = $this->parseParameters($args);
			$this->onRun($sender, $commandLabel, $passArgs);
		}
	}

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

	abstract public function prepare() : void;

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
