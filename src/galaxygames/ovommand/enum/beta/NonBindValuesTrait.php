<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum\beta;

use galaxygames\ovommand\utils\Utils;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;

trait NonBindValuesTrait{
    protected array $values;

    public function __construct(array $values){
        $this->values = Utils::collapseEnumInputs($values);
    }

    public function removeValues(array $context) : void{
        $updates = array_intersect(array_unique($context), $this->values);
        $this->values = array_diff($this->values, $updates);
        if (!empty($updates)) {
            $this->update($updates, UpdateSoftEnumPacket::TYPE_REMOVE);
        }
    }

    public function removeValue(string|int $key) : void{
        $this->removeValues([$key]);
    }

    public function removeSpreadValues(string ...$keys) : void{
        $this->removeValues($keys);
    }

    public function addValues(array $context) : void{
//        $updates = [];
//        foreach ($context as $k => $v) {
//            if (!isset($this->values[$k])) {
//                $this->values[$k] = $v;
//                $updates[] = $k;
//            }
//        }
//        if (isset($updates)) {
//            $this->update($updates, UpdateSoftEnumPacket::TYPE_ADD);
//        }
        /* or use native php function :l
        */
        $updates = array_diff($context, $this->values);
        $this->values = [...$this->values, ...$updates];
        //        $this->values = array_merge($this->values, $updates);
        if (!empty($updates)) {
            $this->update($updates, UpdateSoftEnumPacket::TYPE_ADD);
        }
    }

    public function addValue(string|int $value) : void{
        $this->addValues([$value]);
    }

    abstract protected function update(array $values, int $type);
    abstract public function getValues() : array;
}
