<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

use shared\galaxygames\ovommand\fetus\OvommandException;

final class EnumException extends OvommandException{
	public const ENUM_EMPTY_NAME = 0;
	public const ENUM_NULL_VALUE = 1;
	public const ENUM_INVALID_VALUE_NAME_TYPE = 2;
	public const ENUM_ALREADY_EXISTED = 3;
	public const ENUM_ALIAS_REGISTERED = 4;
	public const ENUM_ALIAS_UNKNOWN_KEY = 5;
	public const ENUM_ALIAS_UNKNOWN_TYPE = 6;
	public const ENUM_INVALID_DEFAULT = 7;
	public const ENUM_EDIT_PROTECTED_ENUM = 8;
}
