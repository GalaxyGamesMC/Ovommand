<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\player\Player;
use pocketmine\Server;

class TargetResult extends BaseResult implements \shared\galaxygames\ovommand\fetus\result\ISucceedResult{
	public const TARGET_ALL = "@a";
	public const TARGET_ENTITIES = "@e";
	public const TARGET_NEAREST_PLAYER = "@p";
	public const TARGET_RANDOM_PLAYER = "@r";
	public const TARGET_SELF = "@s";

	protected string $target;

	public function __construct(string $target){
		$this->target = match ($target) {
			TargetResult::TARGET_ALL => TargetResult::TARGET_ALL,
			TargetResult::TARGET_ENTITIES => TargetResult::TARGET_ENTITIES,
			TargetResult::TARGET_NEAREST_PLAYER => TargetResult::TARGET_NEAREST_PLAYER,
			TargetResult::TARGET_RANDOM_PLAYER => TargetResult::TARGET_RANDOM_PLAYER,
			TargetResult::TARGET_SELF => TargetResult::TARGET_SELF,
			default => $target
		};
	}

	public static function create(string $target) : self{
		return new TargetResult($target);
	}

	/** @return Entity[] */
	public function getTargets(CommandSender $sender) : array{
		if ($this->target === self::TARGET_ALL) {
			return Server::getInstance()->getOnlinePlayers();
		}
		if (!$sender instanceof Living) {
			return [];
		}
		if ($this->target === self::TARGET_NEAREST_PLAYER) {
			$p = $this->getNearestPlayer($sender);
			if ($p === null) {
				return [];
			}
			return [$p];
		}
		return match ($this->target) {
			self::TARGET_ENTITIES => $sender->getWorld()->getEntities(),
			self::TARGET_RANDOM_PLAYER => [$this->getRandomPlayer()],
			self::TARGET_SELF => [$sender],
			default => []
		};
	}

	private function getRandomPlayer() : Player{
		$onlinePlayers = array_values(Server::getInstance()->getOnlinePlayers());
		return $onlinePlayers[mt_rand(0, count($onlinePlayers) - 1)];
	}

	private function getNearestPlayer(CommandSender $entity) : ?Entity{
		$online = array_values(Server::getInstance()->getOnlinePlayers());

		if (!$entity instanceof Player) {
			if (!empty($online)) {
				return $online[array_keys($online)[0]];
			}
			return null;
		}

		if (count($online) === 1) {
			return $entity;
		}
		$entityPos = $entity->getPosition();
		$selectedP = null;
		foreach ($online as $player) {
			if ($player->getWorld()->getDisplayName() !== $entity->getWorld()->getDisplayName()) {
				continue;
			}
			if (!isset($selectedP) || $player->getPosition()->distanceSquared($entityPos) < $selectedP->getPosition()->distanceSquared($entityPos)) {
				$selectedP = $player;
			}
		}
		return $selectedP;
	}
}
