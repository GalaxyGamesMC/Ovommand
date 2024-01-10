<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus;

use pocketmine\plugin\Plugin;

interface IHookable{
	public static function isRegistered() : bool;
	public static function register(Plugin $plugin);
	public static function getOwnedPlugin() : Plugin;
	public static function getInstance() : self;
	public static function getEnumManager();
	public static function isPrivate();
}
