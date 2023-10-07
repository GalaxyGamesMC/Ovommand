<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

use shared\galaxygames\ovommand\fetus\OvommandException;

final class ParameterOrderException extends OvommandException{
	public const PARAMETER_NEGATIVE_ORDER_ERROR = 0;
	public const PARAMETER_DETACHED_ORDER_ERROR = 1;
	public const PARAMETER_DESTRUCTED_ORDER_ERROR = 2;
}
