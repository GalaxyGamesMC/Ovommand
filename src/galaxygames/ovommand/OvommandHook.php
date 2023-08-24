<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\libcommand\parameter\CommandParameters;
use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use muqsit\simplepackethandler\SimplePacketHandler;
use pocketmine\command\Command;
use pocketmine\entity\Attribute;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandOverload;
use pocketmine\plugin\PluginBase;
use ReflectionClass;

final class OvommandHook{
    protected static bool $registered = false;
    protected static PluginBase $plugin;
    protected static EnumManager $enumManager;

    private static array $parameters = [];
    private static array $permissions = [];

    public static function register(PluginBase $plugin) : bool{
        if (!self::$registered || self::$plugin === null || !self::$plugin->isEnabled()) {
            self::$enumManager = EnumManager::getInstance();
            $server = $plugin->getServer();
            $pluginManager = $server->getPluginManager();
            //$pluginManager = Server::getInstance()->getPluginManager();
            try {
                foreach ($server->getCommandMap()->getCommands() as $name => $command) {

                }

                $pluginManager->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event) {
                    $enum = self::$enumManager->getSoftEnum(DefaultEnums::ONLINE_PLAYER);
                    $enum?->addValues($event->getPlayer()->getName());
                }, EventPriority::NORMAL, $plugin);
                $pluginManager->registerEvent(PlayerQuitEvent::class, function(PlayerJoinEvent $event) {
                    $enum = self::$enumManager->getSoftEnum(DefaultEnums::ONLINE_PLAYER);
                    $enum?->removeValues($event->getPlayer()->getName());
                }, EventPriority::NORMAL, $plugin);

                $interceptor = SimplePacketHandler::createInterceptor($plugin);
                $interceptor->interceptOutgoing(function(AvailableCommandsPacket $packet, NetworkSession $target) {
                    $player = $target->getPlayer();
                    if ($player === null) {
                        return;
                    }
                    foreach ($packet->commandData as $name => $commandData) {
                        if (!isset(self::$parameters[$name])) {
                            continue;
                        }
                        $newOverloads = [];
                        foreach (self::$parameters[$name] as $index => $parameter) {
                            $permission = self::$permissions[$name][$index];
                            if ($permission !== null && !$player->hasPermission($permission)) {
                                continue;
                            }
//                            if (is_array($parameter)) {
//                                foreach ($parameter as $p) {
//                                    $newOverloads[] = new CommandOverload(false, $p instanceof CommandParameters ? $p->encode() : [$p]);
//                                }
//                            }
                            $newOverloads[] = new CommandOverload(false, $parameter instanceof CommandParameters ? $parameter->encode() : $parameter);
                        }
                        $commandData->overloads = $newOverloads;
                    }
                });
            } catch (\ReflectionException $e) {
                $plugin->getLogger()->notice($e->getMessage());
                return false;
            }
            self::$plugin = $plugin;
            self::$registered = true;
        }
        return false;
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

    public static function getEnumManager() : EnumManager{
        return self::$enumManager ?? EnumManager::getInstance();
    }
}
