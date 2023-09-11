<?php
declare(strict_types=1);

trait Enemy{
    public function getEnemyName() : string{
        return "Enemy";
    }
}

trait Friend{
    public function getFriendName() : string{
        return "Friend";
    }
}
/**
 * @method getName
 */
class A{
    use Enemy, Friend;
    public function __construct(protected bool $friend = false){
    }

    public function __call(string $name, array $arguments){
        if ($name === "getName") {
            return call_user_func_array(array($this, $this->friend ? "getFriendName" : "getEnemyName"), $arguments);
        }
    }
}

$a = new A(true);
echo $a->getName();