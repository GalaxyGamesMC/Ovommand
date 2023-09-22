<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\fetus\IParametable;
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

final class OvommandHook{
	protected static bool $registered = false;
	protected static Plugin $plugin;
	protected static EnumManager $enumManager;

	public static function register(Plugin $plugin) : bool{
		if (!self::$registered || self::$plugin === null || !self::$plugin->isEnabled()) {
			self::$enumManager = EnumManager::getInstance();
			$pluginManager = Server::getInstance()->getPluginManager();
			try {
				$pluginManager->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event){
					$enum = self::$enumManager->getSoftEnum(DefaultEnums::ONLINE_PLAYER);
					$enum?->addValue($event->getPlayer()->getName());
				}, EventPriority::NORMAL, $plugin);
				$pluginManager->registerEvent(PlayerQuitEvent::class, function(PlayerJoinEvent $event){
					$enum = self::$enumManager->getSoftEnum(DefaultEnums::ONLINE_PLAYER);
					$enum?->removeValue($event->getPlayer()->getName());
				}, EventPriority::NORMAL, $plugin);

				$interceptor = SimplePacketHandler::createInterceptor($plugin);
				$interceptor->interceptOutgoing(function(AvailableCommandsPacket $packet, NetworkSession $target){
					$player = $target->getPlayer();
					if ($player === null) {
						return;
					}
					foreach ($packet->commandData as $name => $commandData) {
						$command = Server::getInstance()->getCommandMap()->getCommand($name);
						if ($command instanceof BaseCommand) {
							foreach ($command->getConstraints() as $constraint) {
								if (!$constraint->isVisibleTo($player)) {
									continue 2;
								}
							}
							$commandData->overloads = self::generateOverloads($player, $command);
						}
					}
				});
			} catch (\ReflectionException $e) {
				$plugin->getLogger()->notice($e->getMessage());
				return false;
			}
			self::$plugin = $plugin;
			self::$registered = true;
			return true;
		}
		return false;
	}

	/**
	 * @param CommandSender $sender
	 * @param BaseCommand   $command
	 *
	 * @return CommandOverload[]
	 */
	private static function generateOverloads(CommandSender $sender, BaseCommand $command) : array{
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
			$scParam = CommandParameter::enum($subCommand->getName(), new CommandEnum("enum#" . spl_object_id($subCommand), [$label, ...$subCommand->getShowAliases()]), 0);
			$overloadList = self::generateOverloadList($subCommand);
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

	private static function generateOverloadList(IParametable $parametable) : array{
		$combinations = [];
		foreach ($parametable->getParameterList() as $parameters) {
			//T1			/** @var CommandParameter[] $params */
			//			$params = [];
			//			foreach ($parameters as $parameter) {
			//				$params[] = $parameter->getNetworkParameterData();
			//			}
			//			$combinations[] = new CommandOverload(false, $params);
			//
			//T2
			//			$params = [];
			//			foreach ($parameters as $parameter) {
			//				$params[] = $parameter->getNetworkParameterData();
			//				if (($parameter instanceof StringEnumParameter) && isset($param->enum) && $param->enum instanceof CommandEnum) {
			//					$refClass = new \ReflectionClass(CommandEnum::class);
			//					$refProp = $refClass->getProperty("enumName");
			//					$refProp->setValue($param->enum, "enum#" . spl_object_id($param->enum));
			//				}
			//			}

			$combinations[] = new CommandOverload(false, array_map(static fn(BaseParameter $parameter) : CommandParameter => $parameter->getNetworkParameterData(), $parameters));
		}

		return $combinations;
	}

	public static function isRegistered() : bool{
		return self::$registered;
	}

	public static function setSyntaxDebugMode(int $syntax) : void{
		SyntaxConst::setSyntax($syntax);
	}

	public static function getEnumManager() : EnumManager{
		return self::$enumManager ?? EnumManager::getInstance();
	}
}
