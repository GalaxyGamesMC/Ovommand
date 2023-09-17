<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\exception\EnumException;
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
		$this->setup();
	}

	protected function setup() : void{
		$this->register(new HardEnum(DefaultEnums::BOOLEAN->value, ["true" => true, "false" => false]));
		$this->register(new HardEnum(DefaultEnums::GAMEMODE->value, GameMode::getAll(),
			["survival" => "0", "creative" => "1", "adventure" => "2", "spectator" => "3"],
			["survival" => "s", "creative" => "c", "adventure" => "a", "spectator" => "v"]));
		$this->register(new SoftEnum(DefaultEnums::ONLINE_PLAYER->value)); // Ideas: event-based closure?
	}

	public function register(SoftEnum|HardEnum $enum, bool $overwrite = false) : void{
		$enumName = $enum->getName();
		if (trim($enumName) === '') {
			throw new EnumException(ExceptionMessage::MSG_ENUM_EMPTY_NAME->getRawErrorMessage(), EnumException::ENUM_EMPTY_NAME_ERROR);
		}

		if (!$overwrite && (isset($this->hardEnums[$enumName]) || isset($this->softEnums[$enumName]))) {
			throw new EnumException(ExceptionMessage::MSG_ENUM_FAILED_OVERLAY->getErrorMessage(["enumName" => $enumName]), EnumException::ENUM_FAILED_OVERLAY_ERROR);
		}
		if ($enum instanceof SoftEnum) {
			if (isset($this->hardEnums[$enumName])) {
				throw new EnumException(ExceptionMessage::MSG_DUPLICATED_NAME_IN_OTHER_TYPE->getErrorMessage(["enumName" => $enumName, "enumType" => HardEnum::class]), EnumException::ENUM_DUPLICATED_NAME_IN_OTHER_TYPE_ERROR);
			}
			$this->softEnums[$enum->getName()] = $enum;
		}
		if ($enum instanceof HardEnum) {
			if (isset($this->softEnums[$enumName])) {
				throw new EnumException(ExceptionMessage::MSG_DUPLICATED_NAME_IN_OTHER_TYPE->getErrorMessage(["enumName" => $enumName, "enumType" => SoftEnum::class]), EnumException::ENUM_DUPLICATED_NAME_IN_OTHER_TYPE_ERROR);
			}
			$this->hardEnums[$enum->getName()] = $enum;
		}
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

//    /**
//     * @deprecated Old API, use either getSoftEnum or getHardEnum for your type
//     */
	public function getEnum(string|DefaultEnums $enumName) {
		if ($enumName instanceof DefaultEnums) {
			$enumName = $enumName->value;
		}
		return $this->enums[$enumName] ?? null;
	}
}
