<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\DefaultEnums;
use galaxygames\ovommand\exception\ParameterException;
use galaxygames\ovommand\OvommandHook;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use galaxygames\ovommand\utils\MessageParser;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use shared\galaxygames\ovommand\fetus\enum\IDynamicEnum;
use shared\galaxygames\ovommand\fetus\enum\IStaticEnum;
use shared\galaxygames\ovommand\fetus\enum\ProtectedEnum;

class EnumParameter extends BaseParameter{
	protected ProtectedEnum|IDynamicEnum|IStaticEnum $enum;

	public function __construct(string $name, DefaultEnums|string $enumName, bool $isSoft = false, bool $optional = false, int $flag = 0){
		$enum = OvommandHook::getEnumManager()->getEnum($enumName, $isSoft);
		if ($enum === null) {
			if ($enumName instanceof DefaultEnums) {
				$enumName = $enumName->value;
			}
			throw new ParameterException(MessageParser::EXCEPTION_PARAMETER_UNKNOWN_ENUM->translate(["enumName" => $enumName, "enumType" => $isSoft ? "soft" : "hard"]), ParameterException::PARAMETER_UNKNOWN_ENUM);
		}
		$this->enum = $enum;
		parent::__construct($name, $optional, $flag);
	}

	public function getValueName() : string{ return $this->enum->getName(); }
	public function getNetworkType() : ParameterTypes{ return $this->isSoft() ? ParameterTypes::SOFT_ENUM : ParameterTypes::ENUM; }
	public function isSoft() : bool{ return $this->enum->isSoft(); }

	public function parse(array $parameters) : ValueResult|BrokenSyntaxResult{
		$result = parent::parse($parameters);
		if ($result instanceof BrokenSyntaxResult) {
			return $result;
		}
		$enumValue = $this->enum->getValue($key = implode(" ", $parameters));
		if ($enumValue !== null) {
			return ValueResult::create($this->returnRaw ? $key : $enumValue);
		}
		return BrokenSyntaxResult::create($key, $key, $this->enum->getName())
			->setCode(BrokenSyntaxResult::CODE_INVALID_INPUTS);
	}

	public function getNetworkParameterData() : CommandParameter{
		return CommandParameter::enum($this->name, $this->enum->encode(), $this->flag, $this->optional);
	}
}
