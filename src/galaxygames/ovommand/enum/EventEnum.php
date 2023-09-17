<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

// Still in works, might be renamed to ClosureEnum?
class EventEnum extends SoftEnum{
    public function __construct(string $name, protected \Closure $closure, array $values = [], array $hiddenAliases = [], array $showAliases = []){
        parent::__construct($name, $values, $hiddenAliases, $showAliases);
    }
}
