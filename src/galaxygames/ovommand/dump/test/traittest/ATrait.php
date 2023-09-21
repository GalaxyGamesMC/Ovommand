<?php
declare(strict_types=1);

namespace traittest;

trait ATrait{
    public function __construct(){}

    public function set(string $a) : void{
        echo $a . PHP_EOL;
    }

    public function et(string $et) : void{
        echo $et . PHP_EOL;
    }
}

trait BTrait{
    public function __construct(){}

    public function set(string $b) : void{
        echo $b . PHP_EOL;
    }
}

/**
 * @method void meow(array $array)
 * @method void set(array $array)
 */

class Test{
    use BTrait, ATrait{ //Why working?
        ATrait::__construct as a__construct;
        ATrait::set as protected r_setA;
        BTrait::__construct as b__construct;
        BTrait::set as protected r_setB;
    }

    public function __call(string $name, array $arguments){
        $this->{$name}($arguments);
    }

    public function __construct(){
        $this->r_setA("hi");
        $this->r_setB("hi");
        $this->set([]);
        $this->meow([]);
        $this->et("");
    }
}
