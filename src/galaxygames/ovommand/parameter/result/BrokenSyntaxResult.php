<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

class BrokenSyntaxResult extends BaseResult{
	public function __construct(protected string $brokenSyntax, protected string $fullSyntax = ""){}

	public static function create(string $brokenSyntax, string $fullSyntax = "") : self{
		return new BrokenSyntaxResult($brokenSyntax, $fullSyntax);
	}

	public function getBrokenSyntax() : string{
		return $this->brokenSyntax;
	}

	public function getFullSyntax() : string{
		return $this->fullSyntax;
	}
}
