<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\OvommandHook;
use pocketmine\player\GameMode;
use pocketmine\plugin\Plugin;
use pocketmine\utils\SingletonTrait;
use shared\galaxygames\ovommand\exception\OvommandEnumPoolException;
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\enum\IStaticEnum;
use shared\galaxygames\ovommand\GlobalEnumPool;

final class EnumManager{
	use SingletonTrait;

	public function __construct(){
		self::setInstance($this);
		$this->initDefaultEnums();
	}

	private function initDefaultEnums() : void{
		GlobalEnumPool::addEnums(OvommandHook::getInstance(),
			new HardEnum("Boolean", ["true" => true, "false" => false],isDefault: true),
			new HardEnum("GameMode",
				["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
				["survival" => "s", "creative" => "c", "adventure" => "a", "spectator" => "v"],
				["survival" => "0", "creative" => "1", "adventure" => "2", "spectator" => "3"],
				isDefault: true
			),
			new HardEnum("PMGameMode",
				["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
				["survival" => "s", "creative" => "c", "adventure" => "a"],
				isDefault: true
			),
			new SoftEnum("OnlinePlayers",isDefault: true)
		);
	}

	public function register(BaseEnum ...$enums) : void{
		foreach ($enums as $enum) {
			$enumName = $enum->getName();
			if ($enum->isDefault()) {
				throw new EnumException("");
			}
			if (trim($enumName) === '') {
				throw new EnumException(ExceptionMessage::MSG_ENUM_EMPTY_NAME->getRawErrorMessage(), EnumException::ENUM_EMPTY_NAME_ERROR);
			}
		}
		try {
			GlobalEnumPool::addEnums(OvommandHook::getInstance(), ...$enums);
		} catch (OvommandEnumPoolException $e) {
			match ($e->getCode()) {
				OvommandEnumPoolException::ENUM_ALREADY_EXISTED_ERROR => throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->value, EnumException::ENUM_ALREADY_EXISTED_ERROR),
				default => throw $e
			};
		}
	}

	public function getSoftEnum(string $enumName) : ?IDynamicEnum{
//		if ($enumName instanceof DefaultEnums) {
//			$enum = $enumName->getEnum();
//			return $enum instanceof IDynamicEnum ? $enum : null;
//		}
		return GlobalEnumPool::getSoftEnum($enumName);
	}

	public function getHardEnum(string $enumName) : ?IStaticEnum{
//		if ($enumName instanceof DefaultEnums) {
//			$enum = $enumName->getEnum();
//			return $enum instanceof IStaticEnum ? $enum : null;
//		}
		return GlobalEnumPool::getHardEnum($enumName);
	}

	public function getEnum(string $enumName, bool $isSoft = false) : IDynamicEnum|IStaticEnum|null{
		return $isSoft ? $this->getSoftEnum($enumName) : $this->getHardEnum($enumName);
	}

	public function getOwningPlugin() : ?Plugin{
		return OvommandHook::getOwnedPlugin();
	}
}
