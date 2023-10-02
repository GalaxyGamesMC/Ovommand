<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

class BrokenSyntaxResult extends BaseResult{
	public function __construct(protected string $brokenSyntax, protected string $fullSyntax = "", protected string $expectedType = "", protected string $preLabel = ""){}

	public static function create(string $brokenSyntax, string $fullSyntax = "", string $expectedType = "", string $preLabel = "") : self{
		return new BrokenSyntaxResult($brokenSyntax, $fullSyntax, $expectedType, $preLabel);
	}

	public function getBrokenSyntax() : string{
		return $this->brokenSyntax;
	}

	public function getFullSyntax() : string{
		return $this->fullSyntax;
	}
}
