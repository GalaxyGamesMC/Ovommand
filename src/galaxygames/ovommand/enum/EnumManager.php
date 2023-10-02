<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\OvommandHook;
use pocketmine\player\GameMode;
use pocketmine\plugin\Plugin;
use pocketmine\utils\SingletonTrait;
use shared\galaxygames\ovommand\enum\fetus\IDynamic;
use shared\galaxygames\ovommand\enum\fetus\IEnum;
use shared\galaxygames\ovommand\enum\fetus\IStatic;
use shared\galaxygames\ovommand\enum\GlobalEnumPool;

final class EnumManager{
	use SingletonTrait;

	public function __construct(){
		self::setInstance($this);
		$this->initDefaultEnums();
	}

	protected function initDefaultEnums() : void{
		$this->register(new HardEnum(DefaultEnums::BOOLEAN->value, ["true" => true, "false" => false]));
		$this->register(new HardEnum(DefaultEnums::GAMEMODE->value,
			["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
			["survival" => "s", "creative" => "c", "adventure" => "a", "spectator" => "v"],
			["survival" => "0", "creative" => "1", "adventure" => "2", "spectator" => "3"]
		));
		$this->register(new HardEnum(DefaultEnums::VANILLA_GAMEMODE->value,
			["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
			["survival" => "s", "creative" => "c", "adventure" => "a"]
		));
		$this->register(new SoftEnum(DefaultEnums::ONLINE_PLAYER->value));
	}

	public function register(IEnum $enum) : void{
		$enumName = $enum->getName();
		if (trim($enumName) === '') {
			throw new EnumException(ExceptionMessage::MSG_ENUM_EMPTY_NAME->getRawErrorMessage(), EnumException::ENUM_EMPTY_NAME_ERROR);
		}
		if ($enum instanceof IDynamic) {
			if (isset(GlobalEnumPool::$softEnums[$enumName])) {
				throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["enumName" => $enumName]), EnumException::ENUM_FAILED_OVERLAY_ERROR);
			}
			GlobalEnumPool::$softEnums[$enum->getName()] = $enum;
		} elseif($enum instanceof IStatic) {
			if (isset(GlobalEnumPool::$hardEnums[$enumName])) {
				throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["enumName" => $enumName]), EnumException::ENUM_FAILED_OVERLAY_ERROR);
			}
			GlobalEnumPool::$hardEnums[$enum->getName()] = $enum;
		} else {
			throw new \RuntimeException("TODO"); //TODO: MSG
		}
	}

	public function getSoftEnum(string|DefaultEnums $enumName) : ?IDynamic{
		if ($enumName instanceof DefaultEnums) {
			$enumName = $enumName->value;
		}
		return GlobalEnumPool::$softEnums[$enumName] ?? null;
	}

	public function getHardEnum(string|DefaultEnums $enumName) : ?IStatic{
		if ($enumName instanceof DefaultEnums) {
			$enumName = $enumName->value;
		}
		return GlobalEnumPool::$hardEnums[$enumName] ?? null;
	}

	public function getEnum(string|DefaultEnums $enumName, bool $preferSoft = true) : IDynamic|IStatic|null{
		if ($enumName instanceof DefaultEnums) {
			$enumName = $enumName->value;
		}
		return $preferSoft ? ($this->getSoftEnum($enumName) ?? $this->getHardEnum($enumName)) : ($this->getHardEnum($enumName) ?? $this->getSoftEnum($enumName));
	}

	public function getOwningPlugin() : ?Plugin{
		return OvommandHook::getOwnedPlugin();
	}
}
