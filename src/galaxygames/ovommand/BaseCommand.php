<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus\beta;

use galaxygames\ovommand\fetus\ParametableTrait;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

abstract class BaseCommand extends Ovommand implements PluginOwned{
	use ParametableTrait;

	public function __construct(protected Plugin $plugin, string $name, Translatable|string $description = "", array $aliases = [], Permission|string|array $permission = null){
		parent::__construct($name, $description, $aliases);
	}

	/**
	 * @param CommandSender                   $sender
	 * @param string                          $label
	 * @param array|array<string,mixed|array> $args
	 */
	abstract public function onRun(CommandSender $sender, string $label, array $args) : void;

	public function getOwningPlugin() : Plugin{
		return $this->plugin;
	}
}
