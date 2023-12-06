<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\OvommandHook;
use galaxygames\ovommand\utils\MessageParser;
use pocketmine\plugin\Plugin;
use shared\galaxygames\ovommand\exception\OvommandEnumPoolException;
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\enum\IStaticEnum;
use shared\galaxygames\ovommand\GlobalEnumPool;

final class EnumManager{
	public function __construct(private readonly OvommandHook $ovommandHook){
		$this->initDefaultEnums();
	}

	private function initDefaultEnums() : void{
		GlobalEnumPool::addEnums($this->ovommandHook, ...array_map(static fn(DefaultEnums $enum) => $enum->encode(), DefaultEnums::cases()));
	}

	public function register(BaseEnum ...$enums) : void{
		foreach ($enums as $enum) {
			$enumName = $enum->getName();
			if ($enum->isDefault()) {
				throw new EnumException(MessageParser::EXCEPTION_ENUM_INVALID_DEFAULT->translate(["enumName" => $enumName]), EnumException::ENUM_INVALID_DEFAULT);
			}
			if (trim($enumName) === '') {
				throw new EnumException(MessageParser::EXCEPTION_ENUM_EMPTY_NAME->value, EnumException::ENUM_EMPTY_NAME);
			}
		}
		try {
			GlobalEnumPool::addEnums(OvommandHook::getInstance(), ...$enums);
		} catch (OvommandEnumPoolException $e) {
			match ($e->getCode()) {
				OvommandEnumPoolException::ENUM_ALREADY_EXISTED => throw new EnumException(MessageParser::EXCEPTION_ENUM_ALREADY_EXISTED->value, EnumException::ENUM_ALREADY_EXISTED),
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

	public function getOwningPlugin() : Plugin{
		return $this->ovommandHook::getOwnedPlugin();
	}

	public function getOvommandHook() : OvommandHook{
		return $this->ovommandHook;
	}
}
