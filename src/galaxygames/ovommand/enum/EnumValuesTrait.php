<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use Composer\Advisory\PartialSecurityAdvisory;
use galaxygames\ovommand\utils\Utils;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;

trait EnumValuesTrait{
    protected array $values;
    protected bool $isBinding;

    public function __construct(array $values){
        $this->isBinding = array_is_list($values);
        $this->values = Utils::collapseEnumInputs($values, $this->isBinding);
    }

    public function removeValue(string|int $key) : void{
        $this->removeValues([$key]);
    }

    public function removeSpreadValues(string ...$keys) : void{
        $this->removeValues($keys);
    }

    public function removeValues(array $context) : void{
        if ($this->isBinding) {
            $updates = [];
            foreach ($context as $k) {
                if (isset($this->values[$k])) {
                    unset($this->values[$k]);
                    $updates[] = $k;
                }
            }
        } else {
            $updates = array_diff($this->values, $context);
        }
        if (isset($updates)) {
            $this->update($updates, UpdateSoftEnumPacket::TYPE_REMOVE);
        }
    }

    public function addValue(string|int $value, mixed $bindValue = null) : void{
        if ($this->isBinding) {
            $this->values[$value] = $bindValue;
        } else {
            $this->values[] = $value;
        }
        $this->update([$value], UpdateSoftEnumPacket::TYPE_ADD);
    }

    public function addValues(array $context) : void{
        if ($this->isBinding) {
            foreach ($context as $k => $v) {
                if (!isset($values1[$k])) {
                    $this->values
                }
            }
        }
    }
}
