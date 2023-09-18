<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

class ErrorResult extends BaseResult{
	public function __construct(protected string $message){}

	public static function create(string $message) : self{
		return new ErrorResult($message);
	}

	public function getMessage() : string{
		return $this->message;
	}
}
