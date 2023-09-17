<?php
declare(strict_types=1);

namespace CortexPE\enum\beta;

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

    public function removeValuesBySpreading(string ...$keys) : void{
        $this->removeValues($keys);
    }

    public function addValues(array $context) : void{
        $updates = array_diff($context, $this->values);
        $this->values = [...$this->values, ...$updates]; //array_merge works too btw
        if (!empty($updates)) {
            $this->update($updates, UpdateSoftEnumPacket::TYPE_ADD);
        }
    }

    public function addValue(string|int $value) : void{
        $this->addValues([$value]);
    }

    public function addValueBySpreading(string|int ...$context) : void{
        $this->addValues($context);
    }

    public function setValues(array $context) : void{
        $this->values = Utils::collapseEnumInputs($context);
        $this->update($this->values, UpdateSoftEnumPacket::TYPE_SET);
    }

    public function setValuesBySpreading(string|int ...$context) : void{
        $this->setValues($context);
    }

    abstract protected function update(array $values, int $type);
    abstract public function getValues() : array;
}
