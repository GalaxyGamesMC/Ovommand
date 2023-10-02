<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\enum;

use shared\galaxygames\ovommand\enum\fetus\IDynamicEnum;
use shared\galaxygames\ovommand\enum\fetus\IStaticEnum;

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
}
