<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;
use pocketmine\Server;

class SoftEnum extends BaseEnum{
	public function encode() : CommandEnum{
		return new CommandEnum($this->name, [...$this->values, ...array_keys($this->showAliases)], true);
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
				unset($this->values[$k]); // move to #1
				$updates[] = $k;
			}
		}
		if (!empty($updates)) {
			// #1
			// $this->values = array_diff($this->values, $updates);
			$this->hiddenAliases = array_diff($this->hiddenAliases, $updates);
			$this->showAliases = array_diff($this->showAliases, $updates);

			$this->update($updates, UpdateSoftEnumPacket::TYPE_REMOVE);
		}
	}

	public function addValue(string $value, mixed $bindValue = null) : void{
		//Should null be default?
		//TODO: aliases support?
		$this->addValues([$value => $bindValue ?? $value]);
	}

	public function addValues(array $context) : void{  //TODO: aliases support?
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

	public function changeValue(string $key, mixed $value) : void{
		if (isset($this->values[$key])) {
			$this->values[$key] = $value;
		}
	}

	private function update(array $values, int $type) : void{
		NetworkBroadcastUtils::broadcastPackets(Server::getInstance()->getOnlinePlayers(), [
			UpdateSoftEnumPacket::create($this->name, $values, $type)
		]);
	}
}
