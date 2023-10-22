<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

use shared\galaxygames\ovommand\fetus\OvommandException;

final class CommandException extends OvommandException{
	public const SUB_COMMAND_DUPLICATE_ALIAS = 0;
}
