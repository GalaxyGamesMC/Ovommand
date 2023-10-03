<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\OvommandHook;
use pocketmine\plugin\Plugin;
use pocketmine\utils\SingletonTrait;
use shared\galaxygames\ovommand\fetus\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\IEnum;
use shared\galaxygames\ovommand\fetus\IStaticEnum;
use shared\galaxygames\ovommand\GlobalEnumPool;
use shared\galaxygames\ovommand\GlobalHookPool;

final class EnumManager{
	use SingletonTrait;

	public function __construct(){
		self::setInstance($this);
		$this->initDefaultEnums();
	}

	private function initDefaultEnums() : void{
		$defaultEnums = DefaultEnums::getAll();
		foreach ($defaultEnums as $defaultEnum) {
			$enum = $defaultEnum->getEnum();
			match (true) {
				$enum instanceof IDynamicEnum => GlobalEnumPool::$softEnums[$enum->getName()] ??= $enum,
				$enum instanceof IStaticEnum => GlobalEnumPool::$hardEnums[$enum->getName()] ??= $enum,
			};
		}
		var_dump(GlobalEnumPool::$hardEnums);
		var_dump(GlobalEnumPool::$softEnums);
		//		try {
		//			$this->register(DefaultEnums::BOOLEAN()->getEnum());
		//			$this->register(DefaultEnums::VANILLA_GAMEMODE()->getEnum());
		//			$this->register(DefaultEnums::PM_GAMEMODE()->getEnum());
		//			$this->register(DefaultEnums::ONLINE_PLAYER()->getEnum());
		//		} catch (EnumException $enumException) {
		//			// DOTHING
		////			if ($enumException->getCode() === EnumException::ENUM_FAILED_OVERLAY_ERROR) {
		////				OvommandHook::getInstance()::getOwnedPlugin()->getLogger()->notice();
		////			}
		//		}
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

	public function attemptFetchingData(string $enumName, bool $isSoft) : IDynamicEnum|IStaticEnum|null{
		$enum = GlobalEnumPool::$softEnums[$enumName] ?? null;
		if ($enum === null) {
			GlobalEnumPool::$cacheFetches[] = OvommandHook::getInstance();
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
