<?php
declare(strict_types=1);

trait Enemy{
    public function getEnemyName() : string{
        return "Enemy";
    }

    public function sayE() : void{
        echo "E\n";
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
//        (new class extends stdClass{})->{$name}(); // Fatal error: Uncaught Error: Call to undefined method stdClass@anonymous::getNaame() in D:\phpstorm2\Ovommand\test\same-function_multiple_methods.php:27
        trigger_error("Uncaught Error: Call to undefined method SoftEnum::{$name}() in " . __FILE__, E_USER_ERROR);
//        throw new \RuntimeException();
    }

    public function say() : void{
        echo "Lmao\n";
    }
}

$a = new A(true);
$a->say();
$a->sayE();
echo $a->getName();

/* with no magic __call()
Fatal error: Uncaught Error: Call to undefined method A::getNaame() in D:\phpstorm2\Ovommand\test\same-function_multiple_methods.php:39
Stack trace:
#0 {main}
  thrown in D:\phpstorm2\Ovommand\test\same-function_multiple_methods.php on line 39
 */