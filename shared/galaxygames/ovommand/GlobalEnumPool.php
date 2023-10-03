<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand;

use shared\galaxygames\ovommand\fetus\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\IHookable;
use shared\galaxygames\ovommand\fetus\IStaticEnum;

final class GlobalEnumPool{
	/**
	 * @var IStaticEnum[]
	 * @phpstan-var array<string, IStaticEnum>
	 */
	public static array $hardEnums = [];
	/**
	 * @var IDynamicEnum[]
	 * @phpstan-var array<string, IDynamicEnum>
	 */
	public static array $softEnums = [];

	/** @var IHookable[] $cacheFetches */
	public static array $cacheFetches = [];

	public static function clearCache() : void{
		self::$cacheFetches = [];
	}
}
