<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\player\GameMode;
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\enum\IStaticEnum;

enum DefaultEnumList : string{
	case BOOLEAN = "Boolean";
	case VANILLA_GAMEMODE = "GameMode";
	case PM_GAMEMODE = "PMGameMode";
	case ONLINE_PLAYER = "OnlinePlayers";

	public function encode() : IDynamicEnum|IStaticEnum{
		return match ($this) {
			self::BOOLEAN => new HardEnum($this->value, ["true" => true, "false" => false]),
			self::VANILLA_GAMEMODE => new HardEnum($this->value,
				["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
				["survival" => "s", "creative" => "c", "adventure" => "a", "spectator" => "v"],
				["survival" => "0", "creative" => "1", "adventure" => "2", "spectator" => "3"]
			),
			self::PM_GAMEMODE => new HardEnum($this->value,
				["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
				["survival" => "s", "creative" => "c", "adventure" => "a"]
			),
			self::ONLINE_PLAYER => new SoftEnum($this->value)
		};
	}

	public function getEnum() : IDynamicEnum|IStaticEnum|null{
		return EnumManager::getInstance()->getEnum($this->value);
	}
}
