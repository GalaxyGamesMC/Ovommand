<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum\beta;

use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;
use pocketmine\Server;
use stdClass;

/**
 * @method void removeValues
 * @method void removeValue
 * @method void removeSpreadValues
 * @method void addValue
 * @method void addValues
 */
class SoftEnum{
    use BindValuesTrait, NonBindValuesTrait{
        BindValuesTrait::__construct as bind___construct;
        BindValuesTrait::removeValues as protected bind_removeValues;
        BindValuesTrait::removeValue as protected bind_removeValue;
        BindValuesTrait::removeSpreadValues as protected bind_removeSpreadValues;
        BindValuesTrait::addValue as protected bind_addValue;
        BindValuesTrait::addValues as protected bind_addValues;
        NonBindValuesTrait::__construct as nonbind___construct;
        NonBindValuesTrait::removeValues as protected nonbind_removeValues;
        NonBindValuesTrait::removeValue as protected nonbind_removeValue;
        NonBindValuesTrait::removeSpreadValues as protected nonbind_removeSpreadValues;
        NonBindValuesTrait::addValue as protected nonbind_addValue;
        NonBindValuesTrait::addValues as protected nonbind_addValues;
    }

    protected bool $isBinding;

    public function __construct(protected string $name, array $values){
        $this->isBinding = array_is_list($values);
        if ($this->isBinding) {
            $this->bind___construct($values);
        } else {
            $this->nonbind___construct($values);
        }
    }

    public function __call(string $name, array $arguments){
        $bind_pre = $this->isBinding() ? "bind_" : "nonbind_";
        $result = match ($name) {
            "removeValues" => call_user_func_array(array($this, $bind_pre . "removeValues", $arguments), $arguments),
            "removeValue" => call_user_func_array(array($this, $bind_pre . "removeValue", $arguments), $arguments),
            "removeSpreadValues" => call_user_func_array(array($this, $bind_pre . "removeSpreadValues", $arguments), $arguments),
            "addValue" => call_user_func_array(array($this, $bind_pre . "addValue", $arguments), $arguments),
            "addValues" => call_user_func_array(array($this, $bind_pre . "addValues", $arguments), $arguments),
            default => null,
        };
        if ($result === null) {
            (new class extends stdClass{})->{$name}();
            // trigger_error("Uncaught Error: Call to undefined method SoftEnum::{$name}() in " . __FILE__, E_USER_ERROR);
        }
        return $result;
    }

    public function parse(string $in) : mixed{
        if ($this->isBinding()) {
            if (isset($this->values[$in])) {
                return $this->values[$in];
            }
            throw new \RuntimeException("TODO"); //Todo: new exceptions
        }
        if (in_array($in, $this->values, true)) {
            return $in;
        }
        throw new \RuntimeException("TODO"); //Todo: new exceptions
    }

    final public function getName() : string{
        return $this->name;
    }

	public function encode() : CommandEnum{
		return new CommandEnum($this->name, $this->values, true);
	}

//    public function addValue(string ...$values) : void{
//        foreach ($values as $k => $v) {
//
//        }
//    }
//
//	public function addValues(array $values) : void{
//		$newValues = [];
//		foreach ($values as $k => $v) {
//			if (!in_array($v, $this->values, true)) {
//				$this->values[] = $v;
//				$newValues[] = $v;
//			}
//		}
//		$this->update($newValues, UpdateSoftEnumPacket::TYPE_ADD);
//	}
//
//	public function setValues(string ...$values) : void{
//		$this->values = array_unique($values);
//		$this->update($this->values, UpdateSoftEnumPacket::TYPE_SET);
//	}

	protected function update(array $values, int $type) : void{
		NetworkBroadcastUtils::broadcastPackets(Server::getInstance()->getOnlinePlayers(), [
			UpdateSoftEnumPacket::create($this->name, $values, $type)
		]);
	}

//    public function hasValue(string $value) : bool{
//        if ($this->isBinding) {
//            return isset($this->values[$value]);
//        }
//        return in_array($value, $this->values, true);
//    }

    public function isBinding() : bool{
        return $this->isBinding;
    }

    public function getValues() : array{
        return $this->values;
    }
}
