<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;
use pocketmine\Server;

class SoftEnum{

    public function __construct(protected string $name, array $values){
        if ($this->isBinding()) {
        }
        $this->values__construct($values);
    }

    public function parse(string $in) : mixed{
        if ($this->isBinding) {
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

    public function addValue(string ...$values) : void{
        foreach ($values as $k => $v) {

        }
    }

	public function addValues(array $values) : void{
		$newValues = [];
		foreach ($values as $k => $v) {
			if (!in_array($v, $this->values, true)) {
				$this->values[] = $v;
				$newValues[] = $v;
			}
		}
		$this->update($newValues, UpdateSoftEnumPacket::TYPE_ADD);
	}

	public function setValues(string ...$values) : void{
		$this->values = array_unique($values);
		$this->update($this->values, UpdateSoftEnumPacket::TYPE_SET);
	}

	/**
	 * @param array $values
	 * @param int   $type
	 *
	 * @return void
	 */
	protected function update(array $values, int $type) : void{
		NetworkBroadcastUtils::broadcastPackets(Server::getInstance()->getOnlinePlayers(), [
			UpdateSoftEnumPacket::create($this->name, $values, $type)
		]);
	}

    public function hasValue(string $value) : bool{
        if ($this->isBinding) {
            return isset($this->values[$value]);
        }
        return in_array($value, $this->values, true);
    }

    public function isBinding() : bool{
        return $this->isBinding;
    }
}
