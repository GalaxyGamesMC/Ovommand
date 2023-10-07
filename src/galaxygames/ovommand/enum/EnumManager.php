<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\OvommandHook;
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
		GlobalEnumPool::addEnums(OvommandHook::getInstance(), ...array_map(static fn(DefaultEnums $enum) => $enum->encode(), DefaultEnums::cases()));
	}

	public function register(BaseEnum ...$enums) : void{
		foreach ($enums as $enum) {
			$enumName = $enum->getName();
			if ($enum->isDefault()) {
				throw new EnumException("");
			}
			if (trim($enumName) === '') {
				throw new EnumException(ExceptionMessage::ENUM_EMPTY_NAME->getRawErrorMessage(), EnumException::ENUM_EMPTY_NAME);
			}
		}
		try {
			GlobalEnumPool::addEnums(OvommandHook::getInstance(), ...$enums);
		} catch (OvommandEnumPoolException $e) {
			match ($e->getCode()) {
				OvommandEnumPoolException::ENUM_ALREADY_EXISTED => throw new EnumException(ExceptionMessage::ENUM_ALREADY_EXISTED->value, EnumException::ENUM_ALREADY_EXISTED),
				default => throw $e
			};
		}
	}

	public function getSoftEnum(DefaultEnums|string $enumName) : ?IDynamicEnum{
		if ($enumName instanceof DefaultEnums) {
			$enumName = $enumName->value;
		}
		return GlobalEnumPool::getSoftEnum($enumName);
	}

	public function getHardEnum(DefaultEnums|string $enumName) : ?IStaticEnum{
		if ($enumName instanceof DefaultEnums) {
			$enumName = $enumName->value;
		}
		return GlobalEnumPool::getHardEnum($enumName);
	}

	public function getEnum(DefaultEnums|string $enumName, bool $isSoft = false) : IDynamicEnum|IStaticEnum|null{
		return $isSoft ? $this->getSoftEnum($enumName) : $this->getHardEnum($enumName);
	}

	public function getOwningPlugin() : ?Plugin{
		return OvommandHook::getOwnedPlugin();
	}
}
