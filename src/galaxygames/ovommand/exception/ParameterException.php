<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

use shared\galaxygames\ovommand\fetus\OvommandException;

final class ParameterException extends OvommandException{
	public const PARAMETER_INVALID_FLAG = 0;
	public const PARAMETER_UNKNOWN_ENUM = 1;
	public const PARAMETER_NON_OPTIONAL_AFTER_OPTIONAL = 2;
	public const PARAMETER_AFTER_TEXT_PARAMETER = 2;
}
