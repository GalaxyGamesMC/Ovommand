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

	/** @var array<string, IDefaultEnum> $defaultEnums */
	protected static array $defaultEnums = [];
	/** @var array<string, IHookable> $defaultEnumsHooker */
	protected static array $defaultEnumsHooker = [];

	public static function addSoftEnums(IHookable $hookable, IDefaultEnum ...$enums) : void{
		if (!GlobalHookPool::isHookRegistered($hookable)) {
			throw new \RuntimeException("Hook is not registered!");
		}
		foreach ($enums as $enum) {
			$eName = $enum->getName();
			if (isset(self::$defaultEnums[$eName])) {
				continue;
			}
			self::$defaultEnums[$eName] = $enum;
			self::$defaultEnumsHooker[$eName] = $hookable;
		}
	}

	public static function addDefaultEnums(IHookable $hookable, IDefaultEnum ...$enums) : void{
		if (!GlobalHookPool::isHookRegistered($hookable)) {
			throw new \RuntimeException("Hook is not registered!");
		}
		foreach ($enums as $enum) {
			$eName = $enum->getName();
			if (isset(self::$defaultEnums[$eName])) {
				continue;
			}
			self::$defaultEnums[$eName] = $enum;
			self::$defaultEnumsHooker[$eName] = $hookable;
		}
	}

	/**
	 * @return array<string, IDefaultEnum>
	 */
	public static function getAllOwnedEnumsOfAHook(IHookable $hookable) : array{
		$results = [];
		foreach (self::$defaultEnums as $enum) {
			$eName = $enum->getName();
			$tHook = self::$defaultEnumsHooker[$eName];

			if ($tHook === $hookable) {
				$results[$eName] = $enum;
			}
		}
		return $results;
	}
}
