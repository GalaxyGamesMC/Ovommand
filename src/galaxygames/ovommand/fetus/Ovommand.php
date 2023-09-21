<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\BaseSubCommand;
use galaxygames\ovommand\constraint\BaseConstraint;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

abstract class Ovommand extends Command implements IParametable, PluginOwned{
	use ParametableTrait;

	/** @var BaseConstraint[] */
	protected array $constraints;
	/** @var Ovommand|null */
	protected ?Ovommand $parent;
	/** @var Ovommand[] subCommands */
	protected array $subCommands = [];
	/** @var CommandSender */
	protected CommandSender $sender;

	public function __construct(Plugin $plugin, string $name, string|Translatable $description = "", array $aliases = []){
		parent::__construct($name, $description, null, $aliases);

		$this->setAliases(array_unique($aliases));
		$this->prepare();
		$this->usageMessage = $this->generateUsageMessage();
	}

	public function registerSubCommand(Ovommand $subCommand) : void{
		if (!isset($this->subCommands[$subName = $subCommand->getName()])) {
			$this->subCommands[$subName] = $subCommand->setParent($this);
			$aliases = $subCommand->getAliases();
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

	final public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if (!$this->testPermission($sender)) {
			return;
		}
	}

	public function getParent() : ?Ovommand{
		return $this->parent;
	}

	public function setParent(?Ovommand $parent) : self{
		$this->parent = $parent;
		return $this;
	}
}
