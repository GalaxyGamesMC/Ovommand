<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;
use pocketmine\Server;
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\enum\ProtectedEnum;
use shared\galaxygames\ovommand\fetus\IHookable;

class SoftEnum extends BaseEnum implements IDynamicEnum{
	public bool $isProtected = false;

	public function encode() : CommandEnum{
		return new CommandEnum($this->name, [...array_keys($this->values), ...array_keys($this->showAliases)], true);
	}

	/** @param array<string, string|string[]> $aliases */
	public function addAliases(array $aliases, bool $isHidden = false, ?IHookable $hookable = null) : void{
		parent::addAliases($aliases, $isHidden, $hookable);
	}

	/** @param string[] $aliases */
	protected function removeAliases(array $aliases, bool $isHidden = false) : void{
		$isHidden ? $aliasesList = &$this->hiddenAliases : $aliasesList = &$this->showAliases;
		foreach ($aliases as $alias) {
			unset($aliasesList[$alias]);
		}
	}

	public function removeValue(string $key) : void{
		$this->removeValues([$key]);
	}

	public function removeValuesBySpreading(string ...$keys) : void{
		$this->removeValues($keys);
	}

	/** @param string[] $context */
	public function removeValues(array $context) : void{
		$updates = [];
		foreach ($context as $k) {
			if (isset($this->values[$k])) {
				unset($this->values[$k]);
				$updates[] = $k;
			}
		}
		if (count($updates) !== 0) {
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
	 * @param array<string, mixed> $context
	 * @param array<string, string|string[]> $showAliases
	 * @param array<string, string|string[]> $hiddenAliases
	 */
	public function addValues(array $context, array $showAliases = [], array $hiddenAliases = []) : void{
		$updates = [];
		foreach ($context as $k => $v) {
			if (!isset($this->values[$k])) {
				$this->values[$k] = $v;
				$updates[] = $k;
			}
		}
		if (count($updates) !== 0) {
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

	public function isProtected() : bool{
		return $this->isProtected;
	}

	public function asProtected() : ProtectedEnum{
		return new ProtectedEnum($this);
	}
}
