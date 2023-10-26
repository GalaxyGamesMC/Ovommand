<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\exception;

use shared\galaxygames\ovommand\fetus\OvommandException;

class OvommandEnumPoolException extends OvommandException{
	public const ENUM_UNREGISTERED_HOOK = 0;
	public const ENUM_ALREADY_EXISTED = 1;
	public const ENUM_UNKNOWN_TYPE = 2;
}
