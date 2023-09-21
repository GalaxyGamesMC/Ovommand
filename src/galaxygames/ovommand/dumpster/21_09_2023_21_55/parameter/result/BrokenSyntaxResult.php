<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

class BrokenSyntaxResult extends BaseResult{
	public function __construct(protected string $brokenSyntax){}

	public static function create(string $brokenSyntax) : self{
		return new BrokenSyntaxResult($brokenSyntax);
	}

	public function getBrokenSyntax() : string{
		return $this->brokenSyntax;
	}
}
