<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\OvommandHook;
use pocketmine\player\GameMode;
use pocketmine\plugin\Plugin;
use pocketmine\utils\SingletonTrait;
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\enum\IEnum;
use shared\galaxygames\ovommand\fetus\enum\IStaticEnum;
use shared\galaxygames\ovommand\GlobalEnumPool;

final class EnumManager{
	use SingletonTrait;

	public function __construct(){
		self::setInstance($this);
		$this->initDefaultEnums();
	}

	private function initDefaultEnums() : void{
		GlobalEnumPool::addDefaultEnums(OvommandHook::getInstance(),
			new DefaultEnum("Boolean", false, ["true" => true, "false" => false]),
			new DefaultEnum("GameMode", false,
				["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
				["survival" => "s", "creative" => "c", "adventure" => "a", "spectator" => "v"],
				["survival" => "0", "creative" => "1", "adventure" => "2", "spectator" => "3"]
			),
			new DefaultEnum("PMGameMode", false,
				["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
				["survival" => "s", "creative" => "c", "adventure" => "a"]
			),
			new DefaultEnum("OnlinePlayers", true)
		);
	}

	public function register(IEnum $enum) : void{
		$enumName = $enum->getName();
		if (trim($enumName) === '') {
			throw new EnumException(ExceptionMessage::MSG_ENUM_EMPTY_NAME->getRawErrorMessage(), EnumException::ENUM_EMPTY_NAME_ERROR);
		}
		if ($enum instanceof IDynamicEnum) {
			if (isset(GlobalEnumPool::$softEnums[$enumName])) {
				throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["enumName" => $enumName]), EnumException::ENUM_FAILED_OVERLAY_ERROR);
			}
			GlobalEnumPool::$softEnums[$enum->getName()] = $enum;
		} elseif($enum instanceof IStaticEnum) {
			if (isset(GlobalEnumPool::$hardEnums[$enumName])) {
				throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["enumName" => $enumName]), EnumException::ENUM_FAILED_OVERLAY_ERROR);
			}
			GlobalEnumPool::$hardEnums[$enum->getName()] = $enum;
		} else {
			throw new \RuntimeException("TODO"); //TODO: MSG
		}
	}

	public function getSoftEnum(string|DefaultEnums $enumName) : ?IDynamicEnum{
		if ($enumName instanceof DefaultEnums) {
			$enum = $enumName->getEnum();
			return $enum instanceof IDynamicEnum ? $enum : null;
		}
		return GlobalEnumPool::$softEnums[$enumName] ?? null;
	}

	public function getHardEnum(string|DefaultEnums $enumName) : ?IStaticEnum{
		if ($enumName instanceof DefaultEnums) {
			$enum = $enumName->getEnum();
			return $enum instanceof IStaticEnum ? $enum : null;
		}
		return GlobalEnumPool::$hardEnums[$enumName] ?? null;
	}

	public function getEnum(string|DefaultEnums $enumName, bool $isSoft = false) : IDynamicEnum|IStaticEnum|null{
		return $isSoft ? $this->getSoftEnum($enumName) : $this->getHardEnum($enumName);
	}

	public function getOwningPlugin() : ?Plugin{
		return OvommandHook::getOwnedPlugin();
	}
}
