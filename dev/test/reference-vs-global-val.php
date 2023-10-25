<?php
declare(strict_types=1);

class Test{
	public function __construct(protected array $a = [], protected array $b = [], protected array $c = []){}

	public function do1(bool $aNOTb = false) : void{
		$aNOTb ? $val = &$this->a : $val = &$this->b;
		$val = [...$val, ...$this->c];
	}

	public function do2(bool $aNOTb = false) : void{
		if ($aNOTb) {
			$this->a = [...$this->a, ...$this->c];
		} else {
			$this->b = [...$this->b, ...$this->c];
		}
	}
}

$a = ["1", "2", "3", 15, 0];
$b = ["5", "6", "7", 10, 1];

$c = ["meow", "bar", "foo", "xex", str_repeat("meowmoew", 1000)];

foreach (range(0, 1000) as $i) {
	$c[] = $i . mt_rand(0, 10000) . "meowae1ihema😭";
}

$test1 = new Test($a, $b, $c);
$test2 = new Test($a, $b, $c);

$test = 500;

$rate = 0;
for ($i = 1; $i <= $test; ++$i) {
	$start = microtime(true);
	$test1->do1();
	$test1->do1(true);
	$end = microtime(true);
	$rate += $end - $start;
}
echo("Test1: " . sprintf('%0.25f', $rate/$test) . PHP_EOL);

$rate = 0;
for ($i = 1; $i <= $test; ++$i) {
	$start = microtime(true);
	$test2->do2();
	$test2->do2(true);
	$end = microtime(true);
	$rate += $end - $start;
}
echo("Test2: " . sprintf('%0.25f', $rate/$test) . PHP_EOL);
