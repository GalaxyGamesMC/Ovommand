<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\enum\ExceptionMessage;
use galaxygames\ovommand\exception\ParameterException;
use galaxygames\ovommand\parameter\type\ParameterTypes;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

abstract class BaseParameter{
	/** @var CommandParameter parameterData */
	protected CommandParameter $parameterData;
	protected int $flag = 0;
	protected ParameterTypes $parameterTypes;

	public function __construct(protected string $name, protected bool $optional = false, int $flag = 0,){
		$this->setFlag($flag);
		$this->parameterData = CommandParameter::standard($this->name, $this->getNetworkType()->value(), $this->flag, $this->optional);
	}

	public function getName() : string{
		return $this->name;
	}

	public function isOptional() : bool{
		return $this->optional;
	}

	abstract public function getNetworkType() : ParameterTypes;

	abstract public function canParse(string $in) : bool;

	abstract public function parse(string $in) : mixed;

	//    abstract public function getPattern() : string;

	public function setFlag(int $flag) : void{
		$this->flag = match ($flag) {
			0, 1 => $flag,
			default => throw new ParameterException(ExceptionMessage::MSG_PARAMETER_INVALID_FLAG->getErrorMessage(['flag' => $flag]), ParameterException::PARAMETER_INVALID_FLAG_ERROR)
		};
	}

	public function getParameterData() : CommandParameter{
		return $this->parameterData;
	}

	public function getSpanLength() : int{
		return 1;
	}

	public function handleQuoteArg() : bool{
		return true;
	}
}
