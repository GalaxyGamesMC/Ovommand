<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;
use pocketmine\Server;
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;

class SoftEnum extends BaseEnum implements IDynamicEnum{

	public function encode() : CommandEnum{
		return new CommandEnum($this->name, [...array_keys($this->values), ...array_keys($this->showAliases)], true);
	}

	public function removeValue(string $key) : void{
		$this->removeValues([$key]);
	}

	public function removeValuesBySpreading(string ...$keys) : void{
		$this->removeValues($keys);
	}

	/** @param string[] $keys */
	public function removeValues(array $keys) : void{
		$updates = [];
		foreach ($keys as $k) {
			if (isset($this->values[$k])) {
				unset($this->values[$k]);
				$updates[] = $k;
			}
		}
		if (!empty($updates)) {
			$this->hiddenAliases = array_diff($this->hiddenAliases, $updates);
			$this->showAliases = array_diff($this->showAliases, $updates);
			$this->update($updates, UpdateSoftEnumPacket::TYPE_REMOVE);
		}
	}

	/**
	 * @param string|string[] $showAliases
	 * @param string|string[] $hiddenAliases
	 */
	public function addValue(string $value, mixed $bindValue = null, string|array $showAliases = [], string|array $hiddenAliases = []) : void{
		$this->addValues([$value => $bindValue ?? $value], [$value => $showAliases], [$value => $hiddenAliases]);
	}

	/**
	 * @param array<string, mixed> $values
	 * @param array<string, string|string[]> $showAliases
	 * @param array<string, string|string[]> $hiddenAliases
	 */
	public function addValues(array $values, array $showAliases = [], array $hiddenAliases = []) : void{
		$updates = [];
		foreach ($values as $k => $v) {
			if (!isset($this->values[$k])) {
				$this->values[$k] = $v;
				$updates[] = $k;
			}
		}
		if (!empty($updates)) {
			$oldShowAliases = $this->showAliases;
			$this->addAliases($showAliases);
			$this->addAliases($hiddenAliases, true);
			// We can also give them the full $this->showAliases too, this might be changed in the near future
			$this->update([...$updates, ...array_diff($this->showAliases, $oldShowAliases)], UpdateSoftEnumPacket::TYPE_ADD);
		}
	}

	public function changeValue(string $key, mixed $value) : void{
		if (isset($this->values[$key])) {
			$this->values[$key] = $value;
		}
	}

	/** @param string[] $values */
	private function update(array $values, int $type) : void{
		NetworkBroadcastUtils::broadcastPackets(Server::getInstance()->getOnlinePlayers(), [
			UpdateSoftEnumPacket::create($this->name, $values, $type)
		]);
	}
}
