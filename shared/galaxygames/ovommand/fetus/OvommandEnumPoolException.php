<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus;

use shared\galaxygames\ovommand\exception\OvommandException;

class OvommandEnumPoolException extends OvommandException{
	public const ENUM_UNREGISTERED_HOOK_ERROR = 0;
	public const ENUM_ALREADY_EXISTED_ERROR = 1;
}
