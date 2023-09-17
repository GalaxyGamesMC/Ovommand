<?php
declare(strict_types=1);

namespace CortexPE\enum\beta;

use galaxygames\ovommand\utils\Utils;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;

trait BindValuesTrait{
    protected array $values;

    public function __construct(array $values){
        $this->values = Utils::collapseEnumInputs($values, true);
    }

    public function removeValue(string|int $key) : void{
        $this->removeValues([$key]);
    }

    public function removeValuesBySpreading(string ...$keys) : void{
        $this->removeValues($keys);
    }

    public function removeValues(array $context) : void{
        $updates = [];
        foreach ($context as $k) {
            if (isset($this->values[$k])) {
                unset($this->values[$k]);
                $updates[] = $k;
            }
        }
        if (!empty($updates)) {
            $this->update($updates, UpdateSoftEnumPacket::TYPE_REMOVE);
        }
    }

    /**
     * @param string|int $value
     * @param mixed|null $bindValue
     *
     * @return void
     */
    public function addValue(string|int $value, mixed $bindValue = null) : void{
        $this->addValues([$value => $bindValue]);
    }

    public function addValuesByStepSpreading(mixed ...$context) : void{
        //        $context = array_values($context);
        $max = count($context);
        if ($max % 2 !== 0) {
            throw new \RuntimeException("Input spreading values must have an even amount/number");
        }
        $out = [];
        for ($i = 0; $i < $max; $i += 2) {
            $key = $context[$i];
            if (!(is_int($key) || is_string($key))) {
                throw new \RuntimeException("Input at $i is not a valid input! Input must be either string or int!");
            }
            $out[$key] = $out[$context[$i + 1]];
        }

        $this->addValues($out);
    }

//    public function addValuesBySpreading(mixed ...$context) : void{
//        $out = [];
//        foreach ($context as $k => $v) {
//            if (!is_string($k)) {
//                continue; //TODO: Exception?
//            }
//            $num = $k;
//            while(is_number)
//            if (str_starts_with((string) $v, "_")) {
//                $out = substr($k, 1,  0);
//            }
//        }
//
//        $this->addValues($out);
//    }
// idea: $this->addValuesBySpreading(x: 1, y:2, _1:2213); => ["x" => 1, "y" => 2, 1 => 2213];

    public function addValues(array $context) : void{
        $updates = [];
        foreach ($context as $k => $v) {
            if (!isset($this->values[$k])) {
                $this->values[$k] = $v;
                $updates[] = $k;
            }
        }
        if (isset($updates)) {
            $this->update($updates, UpdateSoftEnumPacket::TYPE_ADD);
        }
    }

    public function setValues(array $context) : void{
        $this->values = Utils::collapseEnumInputs($context);
        $this->update($this->values, UpdateSoftEnumPacket::TYPE_SET);
    }

    public function changeValue(string $key, mixed $value) : void{
        if (isset($this->values[$key])) {
            $this->values[$key] = $value;
        }
    }

    abstract protected function update(array $values, int $type);
    abstract public function getValues() : array;
}
