<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\fetus\Ovommand;
use galaxygames\ovommand\fetus\ParametableTrait;
use pocketmine\lang\Translatable;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

abstract class BaseCommand extends Ovommand implements PluginOwned{
	use ParametableTrait;

	/**
	 * @param Permission|string|string[]|null $permission
	 */
	public function __construct(
		protected Plugin $plugin, string $name, Translatable|string $description = "",
		Permission|string|array $permission = null, Translatable|string|null $usageMessage = null, array $aliases = []
	){
		parent::__construct($name, $description, $permission, $usageMessage, $aliases);
	}

	public function getOwningPlugin() : Plugin{
		return $this->plugin;
	}
}
