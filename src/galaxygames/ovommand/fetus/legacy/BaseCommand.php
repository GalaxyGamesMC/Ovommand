<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus\legacy;

use galaxygames\ovommand\constraint\BaseConstraint;
use galaxygames\ovommand\fetus\IParametable;
use galaxygames\ovommand\fetus\OvommandTrait;
use galaxygames\ovommand\fetus\ParametableTrait;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

abstract class BaseCommand extends Command implements IParametable, PluginOwned{
	use OvommandTrait;
	use ParametableTrait;

	public function __construct(protected Plugin $plugin, string $name, Translatable|string $description = "", array $aliases = [], Permission|string|array $permission = null){
		parent::__construct($name, $description, null, $aliases);

		$this->prepare();

		if ($permission !== null) {
			$this->setPermission($permission);
		}
	}

	/**
	 * @param CommandSender                   $sender
	 * @param string                          $aliasUsed
	 * @param array|array<string,mixed|array> $args
	 */
	abstract public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void;

	/**
	 * @return BaseSubCommand[]
	 */
	public function getSubCommands() : array{
		return $this->subCommands;
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

	public function getUsageMessage() : string{
		return $this->getUsage();
	}

	public function getOwningPlugin() : Plugin{
		return $this->plugin;
	}
}
