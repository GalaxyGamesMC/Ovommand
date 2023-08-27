<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

class BindingValue{
    public function __construct(
        public string $optionName,
        public mixed $value
    ){}
}
