<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\BaseEnum;
use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\enum\SoftEnum;
use galaxygames\ovommand\parameter\result\BaseResult;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

class EnumParameter extends BaseParameter{
	protected BaseEnum $enum;

	public function __construct(string $name, DefaultEnums|string $enumName, bool $optional = false, int $flag = 0){
		$enumManager = EnumManager::getInstance();
		$enum = $enumManager->getEnum($enumName);
		if ($enum === null) {
			throw new \RuntimeException("Enum is not valid or not registered in Enum Manager"); //TODO: better msg
		}
		$this->enum = $enum;
		parent::__construct($name, $optional, $flag);
	}

	public function getValueName() : string{
		return $this->enum->getName();
	}

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::ENUM;
	}

	public function encodeEnum() : CommandEnum{
		return $this->enum->encode();
	}

	public function isSoft() : bool{
		return $this->enum instanceof SoftEnum;
	}

	public function parse(array $parameters) : BaseResult{
		$enumValue = $this->enum->getValue(implode(" ", $parameters));
		if ($enumValue !== null) {
			return ValueResult::create($enumValue);
		}
		return BrokenSyntaxResult::create("Unknown value!"); //TODO: better msg
	}
}
