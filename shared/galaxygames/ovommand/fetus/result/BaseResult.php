<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\result;

abstract class BaseResult{
	protected int $parsedPoint = 1;

	public function setParsedPoint(int $parsedPoint) : BaseResult{
		$this->parsedPoint = $parsedPoint;
		return $this;
	}

	public function getParsedPoint() : int{
		return $this->parsedPoint;
	}
}
