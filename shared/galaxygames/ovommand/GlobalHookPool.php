<?php
declare(strict_types=1);

/**
 * Ovommand - A command virion for PocketMine-MP
 *                     ､___､
 *                    {O,o}
 *                   /)___)
 *  _____              “ ”	           _
 * |     | _ _  ___  _____  _____  ___  ___ _| |
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
use shared\galaxygames\ovommand\fetus\IHookable;

class GlobalHookPool{
	/** @var IHookable[] */
	protected static array $hooks;

	public static function getHooks() : array{
		return self::$hooks;
	}

	public static function getHook(Plugin $plugin) : IHookable{
		if (!isset(self::$hooks[$pName = $plugin->getName()])) {
			throw new \InvalidArgumentException("OvommandHook of the plugin, named $pName, is not registered!");
		}
		return self::$hooks[$pName];
	}

	public static function addHook(IHookable $hookable) : void{
		$plugin = $hookable->getOwnedPlugin();
		self::$hooks[$plugin->getName()] = $hookable;
	}

	public static function isHookRegistered(IHookable $hookable) : bool{
		return isset(self::$hooks[$hookable::getOwnedPlugin()->getName()]);
	}
}
