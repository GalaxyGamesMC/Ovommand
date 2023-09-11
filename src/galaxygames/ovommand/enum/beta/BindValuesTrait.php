<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum\beta;

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

    public function removeSpreadValues(string ...$keys) : void{
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

    public function addValue(string|int $value, mixed $bindValue = null) : void{
        $this->addValues([$value => $bindValue]);
    }

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

    abstract protected function update(array $values, int $type);
    abstract public function getValues() : array;
}
