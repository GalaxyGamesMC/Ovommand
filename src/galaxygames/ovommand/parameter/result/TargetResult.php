<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\player\Player;
use pocketmine\Server;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

class TargetResult extends BaseResult{ //TODO: Rename to selector?
	public const TARGET_ALL = "@a";
	public const TARGET_ENTITIES = "@e";
	public const TARGET_SELF = "@s";
	public const TARGET_NEAREST_PLAYER = "@p";
	public const TARGET_RANDOM_PLAYER = "@p";

	//	public const DEFAULT_PARAMETERS = [
	//		// Coordinates of the center, will be modified if player in command
	//		"x" => 0,
	//		"y" => 0,
	//		"z" => 0,
	//		"lvl" => "", // The world to search in. Defaults to the current world in case of console, current world in the case of a player
	//		// Search by coordinates
	//		"dx" => 0, // Distance from the center in x (0 = no limit)
	//		"dy" => 0, // Distance from the center in y (0 = no limit)
	//		"dz" => 0, // Distance from the center in z (0 = no limit)
	//		"r" => 0, // Radius max (0 = no limit)
	//		"rm" => 0, // Radius min
	//		// Search by pitch and yaw
	//		"rx" => 180, // Pitch max
	//		"rxm" => 0, // Pitch min
	//		"ry" => 360, // Yaw max
	//		"rym" => 0, // Yaw min
	//		// Count searching
	//		"c" => 0,
	//		// Player parameters searching
	//		"m" => -1, // Gamemode
	//		"l" => PHP_INT_MAX, // Maximum xp level
	//		"lm" => 0, // Min xp level
	//		"name" => "", // Displayed name of the entity (display name for the player, name tag rename for entity)
	//		"type" => "all", // Type of the entity (only for entity selector). "all" means all entities
	//		"tag" => "Health", // Check if the tag exists in the entity/player.
	//	];

	/** @var array<string, mixed> parameters */
	protected array $parameters;

	/**
	 * @param array<string, mixed> $params
	 */
	public function __construct(protected string $target, array $params = []){
		if ($this->isTargetTagged()) {
			$this->parameters = [
				"x" => $this->castPositionValue($params["x"] ?? 0), "y" => $this->castPositionValue($params["y"] ?? 0),
				"z" => $this->castPositionValue($params["z"] ?? 0),
				"dx" => $this->castPositionValue($params["dx"] ?? 0),
				"dy" => $this->castPositionValue($params["dy"] ?? 0),
				"dz" => $this->castPositionValue($params["dz"] ?? 0), "r" => (float) ($params["r"] ?? 0),
				"rm" => (float) ($params["rm"] ?? 0), "rx" => (float) ($params["rx"] ?? 180),
				"rxm" => (float) ($params["rxm"] ?? 0), "ry" => (float) ($params["ry"] ?? 360),
				"rym" => (float) ($params["ry"] ?? 0), "c" => (int) ($params["c"] ?? 0),
				"m" => (string) ($params["c"] ?? 0), "l" => (int) ($params["l"] ?? PHP_INT_MAX),
				"lm" => (int) ($params["lm"] ?? 0), "name" => (string) ($params["name"] ?? null), //?
				"type" => (string) ($params["type"] ?? null), //?
				"tag" => (string) ($params["type"] ?? null), //?
				"world" => $params["world"] ?? "",
			];
			//			$this->bindTargetParameters($params, [
			//				"x" => 0, "y" => 0, "z" => 0, "dx" => 0, "dy" => 0, "dz" => 0, "r" => 0, "rm" => 0, "rx" => 0, "rxm" => 0,
			//				"ry" => 0, "rym" => 0, "c" => 0, "m" => -1, "l" => PHP_INT_MAX, "lm" => 0, "name" => "", "type" => "",
			//				"tag" => "", "world" => "",
			//			]); //TODO: Use this?
		}
	}

	public function castPositionValue(int|float $in) : float{
		if (is_int($in)) {
			return $in + 0.5;
		}
		return $in;
	}

	//	/**
	//	 * @param array $params
	//	 * @param list<mixed> $defaults
	//	 */
	//	private function bindTargetParameters(array $params, array $defaults) : void{
	//		foreach ($defaults as $key => $default) {
	//			if (isset($params[$key])) {
	//				$this->parameters[$key] = match (gettype($default)) {
	//					"boolean" => (bool) $params[$key],
	//					"integer" => (int) $params[$key],
	//					"double" => (float) $params[$key],
	//					"string" => (string) $params[$key],
	//					default => throw new \RuntimeException("Unknown type!")
	//				};
	//			} else {
	//				$this->parameters[$key] = $default;
	//			}
	//			//			$this->parameters[$key] = $params[$key] ?? $default;
	//		}
	//	}

	public static function create(string $target) : self{
		return new TargetResult($target);
	}

	public function isTargetTagged() : bool{ //TODO: better method name?
		return match ($this->target) {
			self::TARGET_ALL, self::TARGET_ENTITIES, self::TARGET_NEAREST_PLAYER, self::TARGET_RANDOM_PLAYER => true,
			default => false
		};
	}

	/**
	 * @return Entity[]
	 */
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
			if ($player->getWorld()->getDisplayName() !== "todo") {
				continue;
			}
			if (!isset($selectedP) || $player->getPosition()->distanceSquared($entityPos) < $selectedP->getPosition()->distanceSquared($entityPos)) {
				$selectedP = $player;
			}
		}
		return $selectedP;
	}
}
