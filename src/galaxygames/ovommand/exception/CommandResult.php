<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

class CommandResult{
	public const RESULT_NO_PARAMETERS_ERROR = 0;
	public const RESULT_INVALID_PARAMETER_ERROR = 1;

	public function __construct(public mixed $data, public int $code = self::RESULT_INVALID_PARAMETER_ERROR){}
}
