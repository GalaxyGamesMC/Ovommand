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
use galaxygames\ovommand\utils\MessageParser;
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
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\IHookable;
use shared\galaxygames\ovommand\GlobalEnumPool;
use shared\galaxygames\ovommand\GlobalHookPool;

final class OvommandHook implements IHookable{
	private static EnumManager $enumManager;
	private static OvommandHook $instance;
	private static Plugin $plugin;
	private static bool $privacy = false;
	private static ?string $namespace = null;

	public static function getInstance() : OvommandHook{
		return self::$instance ?? self::register(self::getOwnedPlugin());
	}

	public static function register(Plugin $plugin, bool $private = false, ?string $namespace = null) : self{
		if (!self::isRegistered() || self::$plugin->isEnabled()) {
			$interceptor = SimplePacketHandler::createInterceptor($plugin, EventPriority::HIGHEST);
			$interceptor->interceptOutgoing(function(AvailableCommandsPacket $packet, NetworkSession $target) use ($plugin) : bool{
				$player = $target->getPlayer();
				if ($player === null) {
					return false;
				}
				$dump = print_r($packet, true);
				file_put_contents("D:\\pmmp\\Ovommand\\dump\\packet\\{$player->getName()}_{$plugin->getName()}_packet_before.txt", $dump);
				$commandMap = Server::getInstance()->getCommandMap();
				foreach ($packet->commandData as $name => $commandData) {
					$command = $commandMap->getCommand($name);
					if (!$command instanceof BaseCommand) {
						continue;
					}
					foreach ($command->getConstraints() as $constraint) {
						if (!$constraint->isVisibleTo($player)) {
							continue 2;
						}
					}
					$commandData->overloads = self::generateOverloads($player, $command);
				}
				$dump = print_r($packet, true);
				file_put_contents("D:\\pmmp\\Ovommand\\dump\\packet\\{$player->getName()}_{$plugin->getName()}packet_after.txt", $dump);
				return true;
			});
			self::$privacy = $private;
			self::$plugin = $plugin;
			self::$instance = new self;
			GlobalHookPool::addHook(self::$instance);
			self::$enumManager = new EnumManager(self::$instance);
			try {
				$pluginManager = Server::getInstance()->getPluginManager();
				$enum = self::$enumManager->getSoftEnum(DefaultEnums::ONLINE_PLAYERS);
				// only the plugin registered that default enum is allowed to update the enum
				if ($enum instanceof IDynamicEnum) {
					$pluginManager->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event) use ($enum, $plugin){
						$enum->addValue($event->getPlayer()->getName());
					}, EventPriority::NORMAL, $plugin);
					$pluginManager->registerEvent(PlayerQuitEvent::class, function(PlayerQuitEvent $event) use ($enum, $plugin) {
						$enum->removeValue($event->getPlayer()->getName());
					}, EventPriority::NORMAL, $plugin);
				}
			} catch (\ReflectionException $e) {
				$plugin->getLogger()->logException($e);
			}
		}
		return self::$instance;
	}

	/** @return CommandOverload[] */
	private static function generateOverloads(CommandSender $sender, Ovommand $command) : array{
		$overloads = [];
		foreach ($command->getSubCommands() as $label => $subCommand) {
			if ($subCommand->getName() !== $label || !$subCommand->testPermissionSilent($sender)) { //get origin label
				continue;
			}
			foreach ($subCommand->getConstraints() as $constraint) {
				if (!$constraint->isVisibleTo($sender)) {
					continue 2;
				}
			}
			$enumName = "scmd#" . spl_object_id($subCommand);
			$vAliasList = $subCommand->getVisibleAliases();
			$scParam = CommandParameter::enum($label, new CommandEnum($enumName, [$label]), 1);
			$overloadList = self::generateOverloads($sender, $subCommand);
			if (empty($overloadList)) {
				$overloads[] = new CommandOverload(false, [$scParam]);
				foreach ($vAliasList as $alias) {
					$overloads[] = new CommandOverload(false, [
						CommandParameter::enum($label, new CommandEnum($enumName . $alias, [$alias]), 1)
					]);
				}
			} else {
				foreach ($overloadList as $overload) {
					$overloads[] = new CommandOverload(false, [$scParam, ...$overload->getParameters()]);
					foreach ($vAliasList as $alias) {
						$overloads[] = new CommandOverload(false, [
							CommandParameter::enum($label, new CommandEnum($enumName . $alias, [$alias]), 1)
							, ...$overload->getParameters()
						]);
					}
				}
			}
		}
		foreach ($command->getOverloads() as $parameters) {
			$overloads[] =  new CommandOverload(false, array_map(static fn(BaseParameter $parameter) : CommandParameter => $parameter->getNetworkParameterData(), $parameters));
		}
		return $overloads;
	}

	public static function isRegistered() : bool{ return isset(self::$plugin); }
	public static function isPrivate() : bool{ return self::$privacy; }
	public static function getEnumManager() : EnumManager{ return self::$enumManager; }

	public static function getOwnedPlugin() : Plugin{
		if (!self::isRegistered()) {
			throw new OvommandHookException(MessageParser::EXCEPTION_OVOMMANDHOOK_NOT_REGISTERED->value);
		}
		return self::$plugin;
	}
}
