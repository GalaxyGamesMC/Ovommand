<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\EnumManager;
use galaxygames\ovommand\exception\EnumException;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use shared\galaxygames\ovommand\fetus\enum\IDefaultEnum;
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\enum\IEnum;
use shared\galaxygames\ovommand\fetus\enum\IStaticEnum;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

class EnumParameter extends BaseParameter{
	protected IDefaultEnum|IEnum $enum;

	public function __construct(string $name, string $enumName, bool $isSoft = false, bool $optional = false, int $flag = 0, protected bool $returnRaw = false){
		$enum = EnumManager::getInstance()->getEnum($enumName, $isSoft);
		if ($enum === null) {
			throw new EnumException("LOL UNKNOWN ENUM! $enumName"); //TODO: Enum, code etc
		}
		$this->enum = $enum;
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
			$this->enum instanceof IDefaultEnum => $this->enum->isSoft(),
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
