<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use shared\galaxygames\ovommand\fetus\result\ISucceedResult;

abstract class BaseResult implements ISucceedResult{
	protected int $parsedPoint = 1;

	public function setParsedPoint(int $parsedPoint) : BaseResult{
		$this->parsedPoint = $parsedPoint;
		return $this;
	}

	public function getParsedPoint() : int{
		return $this->parsedPoint;
	}
}
