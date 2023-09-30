<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\exception\ParameterException;
use galaxygames\ovommand\parameter\result\BaseResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

abstract class BaseParameter{
//	/** @var CommandParameter parameterData */
//	protected CommandParameter $parameterData;
	protected int $flag = 0;
	protected ParameterTypes $parameterTypes;
	protected const REGEX_PATTERN = <<<REGEXP
*
REGEXP; //TODO: find a way to use this?

	public function __construct(protected string $name, protected bool $optional = false, int $flag = 0,){
		$this->setFlag($flag);
	}

	public function getName() : string{
		return $this->name;
	}

	abstract public function getValueName() : string;

	public function isOptional() : bool{
		return $this->optional;
	}

	abstract public function getNetworkType() : ParameterTypes;

	/**
	 * @param string[] $parameters
	 */
	public function parse(array $parameters) : BaseResult{
		if (count($parameters) > $this->getSpanLength()) {
			throw new \InvalidArgumentException("Too many args");
		}
		return new ValueResult("null");
	}

	private function setFlag(int $flag) : void{
		$this->flag = match ($flag) {
			0, 1 => $flag,
			default => throw new ParameterException(ExceptionMessage::MSG_PARAMETER_INVALID_FLAG->getErrorMessage(['flag' => (string) $flag]), ParameterException::PARAMETER_INVALID_FLAG_ERROR)
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
}
