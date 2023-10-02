<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\player\GameMode;
use pocketmine\utils\EnumTrait;
use shared\galaxygames\ovommand\enum\fetus\IDynamicEnum;
use shared\galaxygames\ovommand\enum\fetus\IStaticEnum;

/**
 * @method static DefaultEnums BOOLEAN()
 * @method static DefaultEnums VANILLA_GAMEMODE()
 * @method static DefaultEnums PM_GAMEMODE()
 * @method static DefaultEnums ONLINE_PLAYER()
 */
class DefaultEnums{
	use EnumTrait {
		EnumTrait::__construct as Enum___construct;
	}

	protected static function setup() : void{
		self::registerAll(
			new DefaultEnums("boolean", "Boolean", new HardEnum("Boolean", ["true"=>true, "false"=>false])),
			new DefaultEnums("vanilla_gamemode", "GameMode", new HardEnum("GameMode",
				["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
				["survival" => "s", "creative" => "c", "adventure" => "a", "spectator" => "v"],
				["survival" => "0", "creative" => "1", "adventure" => "2", "spectator" => "3"]
			)),
			new DefaultEnums("pm_gamemode", "PMGameMode", new HardEnum("PMGameMode",
				["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
				["survival" => "s", "creative" => "c", "adventure" => "a"]
			)),
			new DefaultEnums("online_player", "OnlinePlayers", new SoftEnum("OnlinePlayers")),
		);
	}

	private function __construct(
		string $name,
		public readonly string $type,
		public IDynamicEnum|IStaticEnum $enum
	){
		$this->Enum___construct($name);
	}

	public function getType() : string{
		return $this->type;
	}

	public function getEnum() : IDynamicEnum|IStaticEnum{
		return $this->enum;
	}
}