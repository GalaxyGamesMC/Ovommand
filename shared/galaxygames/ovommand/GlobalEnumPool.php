<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand;

use shared\galaxygames\ovommand\fetus\enum\IDefaultEnum;
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\enum\IStaticEnum;
use shared\galaxygames\ovommand\fetus\IHookable;

final class GlobalEnumPool{
	/**
	 * @var array<string, IStaticEnum>
	 */
	public static array $hardEnums = [];
	/**
	 * @var array<string, IDynamicEnum>
	 */
	public static array $softEnums = [];
	public static array $softEnumHooker = [];
	public static array $hardEnumHooker = [];

	public static function addEnums(IHookable $hookable, IDynamicEnum $enum) : void{
		if (!GlobalHookPool::isHookRegistered($hookable)) {
			throw new \RuntimeException("Hook is not registered!");
		}
		if (isset(self::$softEnums[$enum->getName()]) && !$enum->isDeault()) {

		}
	}

//	/** @var array<string, IDefaultEnum> $defaultHardEnums */
//	protected static array $defaultHardEnums = [];
//	/** @var array<string, IDefaultEnum> $defaultSoftEnums */
//	protected static array $defaultSoftEnums = [];
//	/** @var array<string, IHookable> $defaultEnumsHooker */
//	protected static array $defaultSoftEnumsHooker = [];
//	protected static array $defaultHardEnumsHooker = [];
//
//	public static function addDefaultEnums(IHookable $hookable, IDefaultEnum ...$enums) : void{
//		foreach ($enums as $enum) {
//			$eName = $enum->getName();
//			if ($enum->isSoft()) {
//				if (isset(self::$defaultSoftEnums[$eName])) {
//					continue;
//				}
//				self::$defaultSoftEnumsHooker[$eName] = $enum;
//			} else {
//				if (isset(self::$defaultHardEnums[$eName])) {
//					continue;
//				}
//				self::$defaultHardEnums[$eName] = $enum;
//				self::$defaultHardEnumsHooker[$eName] = $hookable;
//			}
//		}
//	}
//
//	/**
//	 * @return array<string, IDefaultEnum>
//	 */
//	public static function getAllOwnedEnumsOfAHook(IHookable $hookable) : array{
//		$results = [];
//		foreach (self::$defaultHardEnums as $enum) {
//			$eName = $enum->getName();
//			$tHook = self::$defaultHardEnumsHooker[$eName];
//
//			if ($tHook === $hookable) {
//				$results[$eName] = $enum;
//			}
//		}
//		return $results;
//	}
}
