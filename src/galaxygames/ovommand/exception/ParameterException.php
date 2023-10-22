<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

use shared\galaxygames\ovommand\fetus\OvommandException;

final class ParameterException extends OvommandException{
	public const PARAMETER_INVALID_FLAG = 0;
	public const PARAMETER_UNKNOWN_ENUM = 1;
}
