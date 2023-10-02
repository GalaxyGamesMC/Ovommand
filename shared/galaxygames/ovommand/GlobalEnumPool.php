<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\enum;

use shared\galaxygames\ovommand\enum\fetus\IDynamic;
use shared\galaxygames\ovommand\enum\fetus\IStatic;

final class GlobalEnumPool{
	/**
	 * @var IStatic[]
	 * @phpstan-var array<string, IStatic>
	 */
	public static array $hardEnums = [];
	/**
	 * @var IDynamic[]
	 * @phpstan-var array<string, IDynamic>
	 */
	public static array $softEnums = [];
}
