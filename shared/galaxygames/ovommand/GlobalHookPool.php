<?php
declare(strict_types=1);

/**
 * Ovommand - A command virion for PocketMine-MP
 *                    ､___､
 *                    {O,o}
 *  _____            /)___)   	              _
 * |     | _ _  ___  _“_”_  _____  ___  ___ _| |
 * |  |  || | ||>_ ||     ||     || .'||   || . |
 * |_____| \_/ |___||_|_|_||_|_|_||__,||_|_||___|
 *
 * Copyright (C) 2023 GalaxyGamesMC
 * @link https://github.com/GalaxyGamesMC/Ovommand
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, @see <https://www.gnu.org/licenses/>.
 */

namespace shared\galaxygames\ovommand;

use pocketmine\plugin\Plugin;
use shared\galaxygames\ovommand\exception\OvommandHookException;
use shared\galaxygames\ovommand\fetus\IHookable;

class GlobalHookPool{
	/** @var IHookable[] */
	private static array $hooks;

	public static function getHook(Plugin $plugin) : IHookable{
		$pid = spl_object_id($plugin);
		if (!isset(self::$hooks[$pid])) {
			throw new \InvalidArgumentException("The ovommandHook of the plugin ($pid), named '" . $plugin->getName() . "', is not registered!");
		}
		if (self::$hooks[$pid]::isPrivate()) {
			throw new OvommandHookException("OvommandHook is private"); //TODO: change msg
		}
		return self::$hooks[$pid];
	}

	public static function addHook(IHookable $hookable) : void{
		self::$hooks[spl_object_id($hookable->getOwnedPlugin())] = $hookable;
	}

	public static function isHookRegistered(IHookable $hookable) : bool{
		return isset(self::$hooks[spl_object_id($hookable::getOwnedPlugin())]);
	}
}
