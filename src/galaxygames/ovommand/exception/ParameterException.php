<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

use shared\galaxygames\ovommand\fetus\exception\OvommandException;

final class ParameterException extends OvommandException{
	public const PARAMETER_INVALID_VALUE_ERROR = 0;
	public const PARAMETER_NO_VALUE_ERROR = 1;
	public const PARAMETER_INVALID_FLAG_ERROR = 2;
}
