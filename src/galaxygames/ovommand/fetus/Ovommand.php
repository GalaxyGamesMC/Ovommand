<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\BaseSubCommand;
use galaxygames\ovommand\constraint\BaseConstraint;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\permission\Permission;
use pocketmine\utils\TextFormat;

abstract class Ovommand extends Command implements IParametable{
	use ParametableTrait;

	/** @var BaseConstraint[] */
	protected array $constraints;
	/** @var Ovommand[] subCommands */
	protected array $subCommands = [];
	/** @var CommandSender */
	protected CommandSender $currentSender;

	public function __construct(string $name, string|Translatable $description = "", array $aliases = [], Permission|string|array $permission = null){
		parent::__construct($name, $description, null, $aliases);

		$this->setAliases(array_unique($aliases));
		if ($permission !== null) {
			$this->setPermission($permission);
		}
		$this->prepare();
		$this->usageMessage = $this->generateUsageMessage();
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param array         $args
	 * @param string        $preLabel Return a string combined of its parent labels with the current label
	 *
	 * @return void
	 */
	final public function execute(CommandSender $sender, string $commandLabel, array $args, string $preLabel = "") : void{
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
				$execute->execute($sender, $label, $args, $preLabel . $commandLabel);
			}
		} else {
			$passArgs = $this->parseParameters($args);
			$this->onRun($sender, $commandLabel, $passArgs);
		}
	}

	abstract public function prepare() : void;

	abstract public function onRun(CommandSender $sender, string $label, array $args) : void;

	public function registerSubCommand(BaseSubCommand $subCommand) : void{
		if (!isset($this->subCommands[$subName = $subCommand->getName()])) {
			$this->subCommands[$subName] = $subCommand->setParent($this);
			$aliases = [...$subCommand->getShowAliases(), ...$subCommand->getHiddenAliases()];
			foreach ($aliases as $alias) {
				if (!isset($this->subCommands[$alias])) {
					$this->subCommands[$alias] = $subCommand;
				} else {
					throw new \InvalidArgumentException("SubCommand with same alias for '$alias' already exists");
				}
			}
		} else {
			throw new \InvalidArgumentException("SubCommand with same name for '$subName' already exists");
		}
	}

	public function getCurrentSender() : CommandSender{
		return $this->currentSender;
	}

	public function setCurrentSender(CommandSender $currentSender) : Ovommand{
		$this->currentSender = $currentSender;
		return $this;
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

	public function getSubCommands() : array{
		return $this->subCommands;
	}
}