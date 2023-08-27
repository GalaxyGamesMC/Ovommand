<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

class IteratorBindingValue implements \IteratorAggregate{
    /** @var array<string,mixed> $options */
    private array $options;

    public function __construct(array $options){
        $this->options = $options;
    }

    public function getIterator() : \ArrayIterator{
        return new \ArrayIterator($this->options);
    }
}
