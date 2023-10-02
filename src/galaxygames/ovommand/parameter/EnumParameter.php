<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\BaseEnum;
use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\parameter\result\BaseResult;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

class EnumParameter extends BaseParameter{
	protected BaseEnum $enum;

	public function __construct(string $name, DefaultEnums|string $enumName, bool $optional = false, int $flag = 0, protected bool $returnRaw = false){
		$enum = EnumManager::getInstance()->getEnum($enumName);
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
		return $this->enum->isSoft() ? ParameterTypes::SOFT_ENUM : ParameterTypes::ENUM;
	}

	public function isSoft() : bool{
		return $this->enum->isSoft();
	}

	public function parse(array $parameters) : BaseResult{
		$enumValue = $this->enum->getValue($key = implode(" ", $parameters));
		if ($enumValue !== null) {
			return ValueResult::create($this->returnRaw ? $key : $enumValue); //TODO: Best sol?
		}
		return BrokenSyntaxResult::create($key, expectedType: $this->enum->getName()); //TODO: better msg
	}

	public function getNetworkParameterData() : CommandParameter{
		return CommandParameter::enum($this->name, $this->enum->encode(), $this->flag, $this->optional);
	}

	public function isReturnRaw() : bool{
		return $this->returnRaw;
	}
}
