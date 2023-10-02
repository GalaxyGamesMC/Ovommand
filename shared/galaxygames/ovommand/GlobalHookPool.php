<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand;

use pocketmine\plugin\Plugin;

class GlobalHookPool{
	/** @var Plugin[] $plugins */
	protected static array $plugins;

	public static function addPlugin(Plugin $plugin) : void{
		self::$plugins[$plugin->getName()] = $plugin;
	}

	public static function getPlugins() : array{
		return self::$plugins;
	}
}
