<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand;

use shared\galaxygames\ovommand\exception\OvommandEnumPoolException;
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\enum\IStaticEnum;
use shared\galaxygames\ovommand\fetus\enum\OvoEnum;
use shared\galaxygames\ovommand\fetus\IHookable;

final class GlobalEnumPool{
	/** @var array<string, IStaticEnum> */
	private static array $hardEnums = [];
	/** @var array<string, IHookable> */
	private static array $hardEnumHooker = [];
	/** @var array<string, IDynamicEnum> */
	private static array $softEnums = [];
	/** @var array<string, IHookable> */
	private static array $softEnumHooker = [];

	public static function addEnums(IHookable $hookable, OvoEnum ...$enums) : void{
		if (!GlobalHookPool::isHookRegistered($hookable)) {
			throw new OvommandEnumPoolException("Hook is not registered!", OvommandEnumPoolException::ENUM_ALREADY_EXISTED);
		}
		foreach ($enums as $enum) {
			$eName = $enum->getName();
			if ($enum->isSoft()) {
				$enumStore = &self::$softEnums;
				$enumHookers = &self::$softEnumHooker;
			} else {
				$enumStore = &self::$hardEnums;
				$enumHookers = &self::$hardEnumHooker;
			}
			if (isset($enumStore[$eName]) && !$enum->isDefault()) {
				throw new OvommandEnumPoolException("Enum with the same name is already existed!", code:OvommandEnumPoolException::ENUM_ALREADY_EXISTED);
			}
			$enumStore[$eName] = $enum;
			$enumHookers[$eName] = $hookable;
		}
	}

	public static function getHardEnum(string $key) : ?IStaticEnum{
		return self::$hardEnums[$key] ?? null;
	}

	public static function getSoftEnum(string $key) : ?IDynamicEnum{
		return self::$softEnums[$key] ?? null;
	}

	/** @return list<IDynamicEnum> */
	public static function getHookerRegisteredSoftEnums(IHookable $hookable) : array{
		$results = [];
		foreach (self::$softEnumHooker as $eName => $hook) {
			if ($hook === $hookable) {
				$results[] = self::$softEnums[$eName];
			}
		}
		return $results;
	}

	/** @return list<IStaticEnum> */
	public static function getHookerRegisteredHardEnums(IHookable $hookable) : array{
		$results = [];
		foreach (self::$hardEnumHooker as $eName => $hook) {
			if ($hook === $hookable) {
				$results[] = self::$hardEnums[$eName];
			}
		}
		return $results;
	}
}
