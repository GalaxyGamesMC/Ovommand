<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\BaseEnum;
use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\parameter\type\ParameterTypes;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

class EnumParameter extends BaseParameter{
	protected BaseEnum $enum;

	public function __construct(string $name, DefaultEnums|string $enum, bool $optional = false, int $flag = 0){
		$enumManager = EnumManager::getInstance();
		$enum = $enumManager->getEnum($enum);
		if ($enum === null) {
			throw new \RuntimeException("Enum is not valid or not registered in Enum Manager"); //TODO: better msg
		}
		$this->enum = $enum;
		parent::__construct($name, $optional, $flag);
	}

	public function getEnumName() : string{
		return $this->enum->getName();
	}

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::ENUM;
	}

	public function encodeEnum() : CommandEnum{
		return $this->enum->encode();
	}

	public function canParse(string $in) : bool{
		return $this->enum->hasValue($in);
	}

	public function parse(string $in) : mixed{
		return $in; //TODO: change replacement
	}
}
