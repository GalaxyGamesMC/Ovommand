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
        if ($this->isBinding) {
            if (isset($this->values[$key])) {
                unset()
            }
        }
        $this->removeValues([$key]);
    }

    public function removeSpreadValues(string ...$keys) : void{
        $this->removeValues($keys);
    }

    /** @noinspection TypeUnsafeArraySearchInspection because of php anti-feature, the auto-casting int string key to int*/
    public function removeValues(array $context) : void{
        $updates = [];

        if ($this->isBinding) {
            foreach ($context as $k) {
                if (isset($values1[$k])) {
                    unset($values1[$k]);
                    $updates[] = $k;
                }
            }
        } else {
            $updates = array_diff($this->values, $context);
        }

//        if ($this->isBinding) {
//            foreach ($context as $v) {
//                if (!(is_int($v) || is_string($v))) {
//                    throw new \RuntimeException("Invalid type!");
//                }
//                if (isset($this->values[$v])) {
//                    unset ($this->values[$v]);
//                    $updates[] = $v;
//                }
//            }
//        } else {
//            foreach ($context as $v) {
//                if (!(is_int($v) || is_string($v))) {
//                    throw new \RuntimeException("Invalid type!");
//                }
//                if (in_array($v, $this->values)) {
//                    array_search($this, $haystack)
//                    unset($this->values[$v]);
//                    $updates[] = $v;
//                }
//            }
//        }
        if (!empty($updates)) {
            $this->update($updates, UpdateSoftEnumPacket::TYPE_REMOVE);
        }
    }

    public function addValue(...$context) : void{
        $newValues = [];
        foreach ($values as $k => $v) {
            if (!in_array($v, $this->values, true)) {
                $this->values[] = $v;
                $newValues[] = $v;
            }
        }
        $this->update($newValues, UpdateSoftEnumPacket::TYPE_ADD);
    }

    public function addValues(array $context) : void{
        $newValues = [];
        foreach ($context as $k => $v) {
            if (!in_array($v, $this->values, true)) {
                $this->values[] = $v;
                $newValues[] = $v;
            }
        }
        $this->update($newValues, UpdateSoftEnumPacket::TYPE_ADD);
    }
}
