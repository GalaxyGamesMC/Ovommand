<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\exception\ParameterException;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use galaxygames\ovommand\utils\MessageParser;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use shared\galaxygames\ovommand\fetus\IParameter;

abstract class BaseParameter implements IParameter{
	protected int $flag = 0;
	/** if this was true, value will not give the parsed results but the raw parameters store in Result */
	protected bool $returnRaw = false;

	final public function getName() : string{
		return $this->name;
	}

	public function __construct(protected string $name, protected bool $optional = false, int $flag = 0){
		$this->setFlag($flag);
	}

	abstract public function getValueName() : string;

	public function isOptional() : bool{
		return $this->optional;
	}

	abstract public function getNetworkType() : ParameterTypes;

	/** @param string[] $parameters */
	public function parse(array $parameters) : BaseResult{
		$cParam = count($parameters);
		$span = $this->getSpanLength();
		return match (true) {
			$cParam > $span => BrokenSyntaxResult::create($parameters[$this->getSpanLength()], implode(" ", $parameters), $this->getValueName())->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS),
			$cParam < $span => BrokenSyntaxResult::create("", implode(" ", $parameters), $this->getValueName())->setCode(BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS),
			default => ValueResult::create($parameters)
		};
	}

	/** Some parameter has 2 or more span but only one can fulfil the required!, like PositionParameter where (~~~ | ~~ ~ | ~ ~ ~) are all acceptable*/
	public function hasCompactParameter() : bool{
		return false;
	}

	private function setFlag(int $flag) : void{
		$this->flag = match ($flag) {
			0, 1 => $flag,
			default => throw new ParameterException(MessageParser::EXCEPTION_PARAMETER_INVALID_FLAG->translate(['flag' => (string) $flag]), ParameterException::PARAMETER_INVALID_FLAG)
		};
	}

	public function getSpanLength() : int{
		return 1;
	}

	public function getFlag() : int{
		return $this->flag;
	}

	public function getNetworkParameterData() : CommandParameter{
		return CommandParameter::standard($this->name, $this->getNetworkType()->value(), $this->flag, $this->optional);
	}

	public function isReturnRaw() : bool{
		return $this->returnRaw;
	}

	public function returnRaw(bool $returnRaw = true) : void{
		$this->returnRaw = $returnRaw;
	}
}
