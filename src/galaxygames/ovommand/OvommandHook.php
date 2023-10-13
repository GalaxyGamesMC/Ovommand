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

namespace galaxygames\ovommand;

use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\utils\syntax\SyntaxConst;
use muqsit\simplepackethandler\SimplePacketHandler;
use pocketmine\command\CommandSender;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandOverload;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use shared\galaxygames\ovommand\exception\OvommandHookException;
use shared\galaxygames\ovommand\fetus\IHookable;
use shared\galaxygames\ovommand\GlobalHookPool;

final class OvommandHook implements IHookable{
	/** @var self */
	private static IHookable $instance;
	protected static Plugin $plugin;
	protected static EnumManager $enumManager;

	public static function getInstance() : OvommandHook{
		if (!self::isRegistered()) {
			throw new OvommandHookException("This OvommandHook is not registered with a plugin; please hook it to a plugin before using it for your own goods.");
		}
		return self::$instance ?? self::register(self::$plugin);
	}

	public static function register(Plugin $plugin) : self{
		if (!self::isRegistered() || self::$plugin->isEnabled()) {
			$interceptor = SimplePacketHandler::createInterceptor($plugin);
			$interceptor->interceptOutgoing(function(AvailableCommandsPacket $packet, NetworkSession $target) : bool{
				$player = $target->getPlayer();
				if ($player === null) {
					return false;
				}
				$commandMap = Server::getInstance()->getCommandMap();
				foreach ($packet->commandData as $name => $commandData) {
					$command = $commandMap->getCommand($name);
					if ($command instanceof BaseCommand) {
						foreach ($command->getConstraints() as $constraint) {
							if (!$constraint->isVisibleTo($player)) {
								continue 2;
							}
						}
						$commandData->overloads = self::generateOverloads($player, $command);
					}
				}
				return true;
			});
			self::$plugin = $plugin;
			self::$instance = new self;
			GlobalHookPool::addHook(self::$instance);

			self::$enumManager = EnumManager::getInstance();
			$pluginManager = Server::getInstance()->getPluginManager();
			try {
				$pluginManager->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event){
					$enum = self::$enumManager->getSoftEnum(DefaultEnums::ONLINE_PLAYERS);
					$enum?->addValue($event->getPlayer()->getName());
				}, EventPriority::NORMAL, $plugin);
				$pluginManager->registerEvent(PlayerQuitEvent::class, function(PlayerQuitEvent $event){
					$enum = self::$enumManager->getSoftEnum(DefaultEnums::ONLINE_PLAYERS);
					$enum?->removeValue($event->getPlayer()->getName());
				}, EventPriority::NORMAL, $plugin);
			} catch (\ReflectionException $e) {
				$plugin->getLogger()->logException($e);
			}
		}
		return self::$instance;
	}

	/**
	 * @param CommandSender $sender
	 * @param Ovommand   $command
	 *
	 * @return CommandOverload[]
	 */
	private static function generateOverloads(CommandSender $sender, Ovommand $command) : array{
		$overloads = [];

		foreach ($command->getSubCommands() as $label => $subCommand) {
			if ($subCommand->isAliases($label) || !$subCommand->testPermissionSilent($sender)) { //Get origin label
				continue;
			}
			foreach ($subCommand->getConstraints() as $constraint) {
				if (!$constraint->isVisibleTo($sender)) {
					continue 2;
				}
			}
			$overloadList = self::generateOverloads($sender, $subCommand);
			$scParam = CommandParameter::enum($subCommand->getName(), new CommandEnum("enum#" . spl_object_id($subCommand), [$label, ...array_values($subCommand->getShowAliases())]), 1);

			if (!empty($overloadList)) {
				foreach ($overloadList as $overload) {
					$overloads[] = new CommandOverload(false, [$scParam, ...$overload->getParameters()]);
				}
			} else {
				$overloads[] = new CommandOverload(false, [$scParam]);
			}

			// v2
			foreach ($subCommand->getShowAliases() as $alias) {

			}
			if (!empty($overloadList)) {
				foreach ($overloadList as $overload) {
					$overloads[] = new CommandOverload(false, [$scParam, ...$overload->getParameters()]);
				}
			} else {
				$overloads[] = new CommandOverload(false, [$scParam]);
			}
		}

		foreach (self::generateOverloadList($command) as $overload) {
			$overloads[] = $overload;
		}

		return $overloads;
	}

	/**
	 * @return CommandOverload[]
	 */
	private static function generateOverloadList(Ovommand $ovommand) : array{
		$combinations = [];
		foreach ($ovommand->getOverloads() as $parameters) {
			$combinations[] = new CommandOverload(false, array_map(static fn(BaseParameter $parameter) : CommandParameter => $parameter->getNetworkParameterData(), $parameters));
		}

		return $combinations;
	}

	public static function isRegistered() : bool{
		return isset(self::$plugin);
	}

	public static function setSyntaxDebugMode(int $syntax) : void{
		SyntaxConst::setSyntax($syntax);
	}

	public static function getEnumManager() : EnumManager{
		return self::$enumManager ?? EnumManager::getInstance();
	}

	public static function getOwnedPlugin() : Plugin{
		if (!self::isRegistered()) {
			throw new OvommandHookException("This OvommandHook is not registered with a plugin; please hook it to a plugin before using it for your own goods.");
		}
		return self::$plugin;
	}
}
