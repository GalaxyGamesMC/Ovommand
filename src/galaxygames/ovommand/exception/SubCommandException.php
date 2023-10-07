<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

use shared\galaxygames\ovommand\fetus\OvommandException;

final class SubCommandException extends OvommandException{
	public const SUB_COMMAND_DUPLICATE_NAME_ERROR = 0;
	public const SUB_COMMAND_DUPLICATE_ALIAS_ERROR = 1;
}
