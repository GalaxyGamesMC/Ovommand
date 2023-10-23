<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\result;

abstract class BaseResult{
	protected int $parsedID = 1;

	public function setParsedID(int $parsedID) : BaseResult{
		$this->parsedID = $parsedID;
		return $this;
	}

	public function getParsedID() : int{
		return $this->parsedID;
	}
}
