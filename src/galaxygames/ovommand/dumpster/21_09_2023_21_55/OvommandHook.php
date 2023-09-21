<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\fetus\IParametable;
use galaxygames\ovommand\syntax\SyntaxConst;
use muqsit\simplepackethandler\SimplePacketHandler;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandOverload;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

final class OvommandHook{
	protected static bool $registered = false;
	protected static PluginBase $plugin;
	protected static EnumManager $enumManager;

	public static function register(PluginBase $plugin) : bool{
		if (!self::$registered || self::$plugin === null || !self::$plugin->isEnabled()) {
			self::$enumManager = EnumManager::getInstance();
			$server = $plugin->getServer();
			$pluginManager = $server->getPluginManager();
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
								//TODO: Add constraints
							}
							$commandData->overloads = self::generateOverloads($command);
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
	 * @param BaseCommand $command
	 *
	 * @return CommandOverload[]
	 */
	private static function generateOverloads(BaseCommand $command) : array{
		$overloads = [];

		foreach ($command->getSubCommands() as $label => $subCommand) {
			if (!$subCommand->testPermissionSilent(...) || $subCommand->getName() !== $label) { // hide aliases
				continue;
			}
			foreach ($subCommand->getConstraints() as $constraint) {
				if (!$constraint->isVisibleTo($cs)) {
					continue 2;
				}
			}
			$scParam = new CommandParameter();
			$scParam->paramName = $label;
			$scParam->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | AvailableCommandsPacket::ARG_FLAG_ENUM;
			$scParam->isOptional = false;
			$scParam->enum = new CommandEnum($label, [$label]);

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
		$input = $parametable->getParameterList();
		$combinations = [];
		$outputLength = array_product(array_map("count", $input));
		$indexes = [];
		foreach ($input as $k => $charList) {
			$indexes[$k] = 0;
		}
		do {
			/** @var CommandParameter[] $set */
			$set = [];
			foreach ($indexes as $k => $index) {
				//                $param = $set[$k] = clone $input[$k][$index]->getNetworkParameterData();

				if (isset($param->enum) && $param->enum instanceof CommandEnum) {
					$refClass = new \ReflectionClass(CommandEnum::class);
					//					$refProp = $refClass->getProperty("enumName");
					//					$refProp->setAccessible(true);
					//					$refProp->setValue($param->enum, "enum#" . spl_object_id($param->enum));
				}
			}
			$combinations[] = new CommandOverload(false, $set);

			foreach ($indexes as $k => $v) {
				$indexes[$k]++;
				$lim = count($input[$k]);
				if ($indexes[$k] >= $lim) {
					$indexes[$k] = 0;
					continue;
				}
				break;
			}
		} while (count($combinations) !== $outputLength);

		return $combinations;
	}

	//    private static function solveAttributeCommand(string $name, Command $command) : void{
	//        $ref = new ReflectionClass($command);
	//        foreach ($ref->getAttributes(CommandParameters::class) as $attribute) {
	//            /** @var CommandParameters $parameters */
	//            $parameters = $attribute->newInstance();
	//
	//            self::$parameters[$name][] = $parameters->hasSoftEnum() ? $parameters : $parameters->encode();
	//            self::$permissions[$name][] = $parameters->getPermission();
	//        }
	//    }

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
