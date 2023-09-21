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
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

class StringEnumParameter extends BaseParameter{
	protected BaseEnum $enum;

	public function __construct(string $name, protected array $values, bool $optional = false, int $flag = 0){
		parent::__construct($name, $optional, $flag);
	}

	public function getValueName() : string{
		return "enum#" . spl_object_id($this);
	}

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::ENUM;
	}

	public function encodeEnum() : CommandEnum{
		return new CommandEnum($this->getValueName(), $this->values);
	}

	public function parse(array $parameters) : BaseResult{
		$in = implode(" ", $parameters);
		$enumValue = $this->values[$in] ?? null;
		if ($enumValue !== null) {
			return ValueResult::create($enumValue);
		}
		return BrokenSyntaxResult::create("Unknown value!"); //TODO: better msg
	}

	public function getNetworkParameterData() : CommandParameter{
		return CommandParameter::enum($this->name, $this->encodeEnum(), $this->flag, $this->optional);
	}
}
