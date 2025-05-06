<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use galaxygames\ovommand\utils\Utils;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

class StringEnumParameter extends BaseParameter{
	/** @var list<string> */
	protected array $values;
	/** @param string[] $values */
	public function __construct(string $name, array $values, bool $optional = false, int $flag = 0){
		parent::__construct($name, $optional, $flag);
		$this->values = Utils::uniqueList($values);
	}

	public function getValueName() : string{ return "enum#" . spl_object_id($this); }
	public function getNetworkType() : ParameterTypes{ return ParameterTypes::ENUM; }

	public function encodeEnum() : CommandEnum{
		return new CommandEnum($this->getValueName(), $this->values);
	}

	public function parse(array $parameters) : ValueResult|BrokenSyntaxResult{
		$in = implode(" ", $parameters);
		$enumValue = $this->values[$in] ?? null;
		if ($enumValue !== null) {
			return ValueResult::create($enumValue);
		}
		return BrokenSyntaxResult::create($in);
	}

	public function getNetworkParameterData() : CommandParameter{
		return CommandParameter::enum($this->name, $this->encodeEnum(), $this->flag, $this->optional);
	}
}
