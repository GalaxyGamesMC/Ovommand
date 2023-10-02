<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\exception\ExceptionMessage;
use pocketmine\player\GameMode;
use pocketmine\utils\SingletonTrait;

final class EnumManager{
	use SingletonTrait;

	/**
	 * @var HardEnum[]
	 * @phpstan-var array<string, HardEnum>
	 */
	private array $hardEnums = [];
	/**
	 * @var SoftEnum[]
	 * @phpstan-var array<string, SoftEnum>
	 */
	private array $softEnums = [];

	public function __construct(){
		self::setInstance($this);
		$this->initDefaultEnums();
	}

	protected function initDefaultEnums() : void{
		$this->register(new HardEnum(DefaultEnums::BOOLEAN->value, ["true" => true, "false" => false]));
		$this->register(new HardEnum(DefaultEnums::GAMEMODE->value,
			["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
			["survival" => "s", "creative" => "c", "adventure" => "a", "spectator" => "v"],
			["survival" => "0", "creative" => "1", "adventure" => "2", "spectator" => "3"]
		));
		$this->register(new HardEnum(DefaultEnums::VANILLA_GAMEMODE->value,
			["survival" => GameMode::SURVIVAL(), "creative" => GameMode::CREATIVE(), "adventure" => GameMode::ADVENTURE(), "spectator" => GameMode::SPECTATOR()],
			["survival" => "s", "creative" => "c", "adventure" => "a"]
		));
		$this->register(new SoftEnum(DefaultEnums::ONLINE_PLAYER->value));
	}

	public function register(SoftEnum|HardEnum $enum) : void{
		$enumName = $enum->getName();
		if (trim($enumName) === '') {
			throw new EnumException(ExceptionMessage::MSG_ENUM_EMPTY_NAME->getRawErrorMessage(), EnumException::ENUM_EMPTY_NAME_ERROR);
		}
		if ($enum instanceof SoftEnum) {
			if (isset($this->softEnums[$enumName])) {
				throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["enumName" => $enumName]), EnumException::ENUM_FAILED_OVERLAY_ERROR);
			}
			$this->softEnums[$enum->getName()] = $enum;
		} else {
			if (isset($this->hardEnums[$enumName])) {
				throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["enumName" => $enumName]), EnumException::ENUM_FAILED_OVERLAY_ERROR);
			}
			$this->hardEnums[$enum->getName()] = $enum;
		}
//		$enum->isSoft() ? $enumList = &$this->softEnums : $enumList = &$this->hardEnums; reference method, slower?
//		if (isset($enumList[$enumName])) {
//			throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["enumName" => $enumName]), EnumException::ENUM_FAILED_OVERLAY_ERROR);
//		}
//		$enumList[$enum->getName()] = $enum;
	}

	public function getSoftEnum(string|DefaultEnums $enumName) : ?SoftEnum{
		if ($enumName instanceof DefaultEnums) {
			$enumName = $enumName->value;
		}
		return $this->softEnums[$enumName] ?? null;
	}

	public function getHardEnum(string|DefaultEnums $enumName) : ?HardEnum{
		if ($enumName instanceof DefaultEnums) {
			$enumName = $enumName->value;
		}
		return $this->hardEnums[$enumName] ?? null;
	}

	public function getEnum(string|DefaultEnums $enumName, bool $preferSoft = true) : ?BaseEnum{
		if ($enumName instanceof DefaultEnums) {
			$enumName = $enumName->value;
		}
		return $preferSoft ? $this->softEnums[$enumName] ?? $this->hardEnums[$enumName] ?? null : $this->hardEnums[$enumName] ?? $this->softEnums[$enumName] ?? null;
	}
}
