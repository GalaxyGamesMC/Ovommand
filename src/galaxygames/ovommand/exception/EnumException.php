<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

use shared\galaxygames\ovommand\fetus\OvommandException;

final class EnumException extends OvommandException{
	public const ENUM_EMPTY_NAME = 0;
	public const ENUM_INVALID_VALUE_NAME_TYPE = 1;
	public const ENUM_ALREADY_EXISTED = 2;
	public const ENUM_ALIAS_REGISTERED = 3;
	public const ENUM_ALIAS_UNKNOWN_KEY = 4;
	public const ENUM_ALIAS_UNKNOWN_TYPE = 5;
	public const ENUM_INVALID_DEFAULT = 6;
}
