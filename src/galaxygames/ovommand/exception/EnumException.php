<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

use shared\galaxygames\ovommand\fetus\exception\OvommandException;

final class EnumException extends OvommandException{
	public const ENUM_EMPTY_NAME_ERROR = 0;
	public const ENUM_FAILED_OVERLAY_ERROR = 1;
	public const ENUM_DUPLICATED_NAME_IN_OTHER_TYPE_ERROR = 2; // lol
	public const ENUM_ALIAS_REGISTERED_ERROR = 3;
	public const ENUM_ALIAS_UNKNOWN_KEY_ERROR = 4;
	public const ENUM_ALIAS_UNKNOWN_TYPE_ERROR = 5;
}
