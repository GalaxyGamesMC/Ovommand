<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

class sqlBindingValue extends \SplFixedArray{
	public function __construct(array $values){
		parent::__construct($size = count($values));
		foreach (array_keys($values) as $key) {

		}
	}
}
