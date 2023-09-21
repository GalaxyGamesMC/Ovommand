<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

class ParentedParameter extends BaseParameter{
	public function __construct(string $parent, protected BaseParameter $childParameter){
		parent::__construct($childParameter->getName(), $childParameter->isOptional(), $childParameter->getFlag());
	}

	public function getValueName() : string{
		$this->childParameter->getValueName();
	}

	public function getNetworkType() : ParameterTypes{
		$this->childParameter->getNetworkType();
	}

	public function getChildParameter() : \galaxygames\ovommand\parameter\BaseParameter{
		return $this->childParameter;
	}
}
