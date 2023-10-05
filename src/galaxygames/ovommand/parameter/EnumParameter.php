<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use shared\galaxygames\ovommand\fetus\BaseResult;
use shared\galaxygames\ovommand\fetus\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\IEnum;
use shared\galaxygames\ovommand\fetus\IStaticEnum;

class EnumParameter extends BaseParameter{
	protected ?IEnum $enum;

	public function __construct(string $name, DefaultEnums|string $enumName, bool $isSoft = false, bool $optional = false, int $flag = 0, protected bool $returnRaw = false){
		$this->enum = EnumManager::getInstance()->getEnum($enumName, $isSoft);
		if ($this->enum === null) {
			throw new EnumException("LOL UNKNOWN ENUM!"); //TODO: Enum, code etc
		}
		parent::__construct($name, $optional, $flag);
	}

	public function getValueName() : string{
		return $this->enum->getName();
	}

	public function getNetworkType() : ParameterTypes{
		return $this->isSoft() ? ParameterTypes::SOFT_ENUM : ParameterTypes::ENUM;
	}

	public function isSoft() : bool{
		return match (true) {
			$this->enum instanceof IStaticEnum => false,
			$this->enum instanceof IDynamicEnum => true,
			default => throw new \RuntimeException("TODO") //TODO: Update msg
		};
	}

	public function parse(array $parameters) : BaseResult{
		$enumValue = $this->enum->getValue($key = implode(" ", $parameters));
		if ($enumValue !== null) {
			return ValueResult::create($this->returnRaw ? $key : $enumValue); //TODO: Best sol?
		}
		return BrokenSyntaxResult::create($key, $key, expectedType: $this->enum->getName()); //TODO: better msg
	}

	public function getNetworkParameterData() : CommandParameter{
		return CommandParameter::enum($this->name, $this->enum->encode(), $this->flag, $this->optional);
	}

	public function isReturnRaw() : bool{
		return $this->returnRaw;
	}
}
