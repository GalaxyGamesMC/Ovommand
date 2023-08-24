<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;
use pocketmine\Server;

class SoftEnum{
    use CommandEnumTrait;

	public function encode() : CommandEnum{
		return new CommandEnum($this->name, $this->values, true);
	}

	public function addValues(string ...$values) : void{
		$newValues = [];
		foreach ($values as $v) {
			if (!in_array($v, $this->values, true)) {
				$this->values[] = $v;
				$newValues[] = $v;
			}
		}
		$this->update($newValues, UpdateSoftEnumPacket::TYPE_ADD);
	}

	public function removeValues(string ...$values) : void{
        $removeValues = [];
		foreach ($values as $key => $value) {
			if (in_array($value, $this->values, true)) {
                $removeValues[] = $value;
				unset($this->values[$key]);
			}
		}
		$this->update($removeValues, UpdateSoftEnumPacket::TYPE_REMOVE);
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
}
