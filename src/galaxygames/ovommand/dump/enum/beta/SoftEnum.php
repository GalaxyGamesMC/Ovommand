<?php
declare(strict_types=1);

namespace CortexPE\enum\beta;

use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;
use pocketmine\Server;

/**
 * @method void removeValues(array $context)
 * @method void removeValue(string|int $key)
 * @method void removeValuesBySpreading(string|int ...$key)
 * @method void addValue(string|int $value, mixed $bindValue = null)
 * @method void addValues(array $context)
 * @method void setValues(array $context)
 */
class SoftEnum{
	use BindValuesTrait, NonBindValuesTrait {
		BindValuesTrait::__construct as bind___construct;
		BindValuesTrait::removeValues as protected bind_removeValues;
		BindValuesTrait::removeValue as protected bind_removeValue;
		BindValuesTrait::removeValuesBySpreading as protected bind_removeValuesBySpreading;
		BindValuesTrait::addValue as protected bind_addValue;
		BindValuesTrait::addValues as protected bind_addValues;
		BindValuesTrait::setValues as protected bind_setValues;
		NonBindValuesTrait::__construct as nonbind___construct;
		NonBindValuesTrait::removeValues as protected nonbind_removeValues;
		NonBindValuesTrait::removeValue as protected nonbind_removeValue;
		NonBindValuesTrait::removeValuesBySpreading as protected nonbind_removeValuesBySpreading;
		NonBindValuesTrait::addValue as protected nonbind_addValue;
		NonBindValuesTrait::addValues as protected nonbind_addValues;
		NonBindValuesTrait::setValues as protected nonbind_setValues;
	}

	protected bool $isBinding;

	public function __construct(protected string $name, array $values){
		$this->isBinding = array_is_list($values);
		if ($this->isBinding) {
			$this->bind___construct($values);
		} else {
			$this->nonbind___construct($values);
		}
		$this->changeValue("a", 2);
	}

	public function __call(string $name, array $arguments){
		$bind_pre = $this->isBinding() ? "bind_" : "nonbind_";
		$result = match ($name) {
			"addValueBySpreading" => $this->isBinding() ? null : $this->{$bind_pre . $name}($arguments),
			"removeValues", "removeValue", "removeValuesBySpreading", "addValue", "addValues", "setValues" => $this->{$bind_pre . $name}($arguments),
			default => null,
		};

		if ($result === null) {
			(new class()extends \stdClass{})->{$name}();
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

	protected function update(array $values, int $type) : void{
		NetworkBroadcastUtils::broadcastPackets(Server::getInstance()->getOnlinePlayers(), [
			UpdateSoftEnumPacket::create($this->name, $values, $type)
		]);
	}

	public function hasValue(string|int $value) : bool{
		if ($this->isBinding) {
			return isset($this->values[$value]);
		}
		return in_array($value, $this->values, true);
	}

	public function isBinding() : bool{
		return $this->isBinding;
	}

	public function getValues() : array{
		return $this->values;
	}
}
