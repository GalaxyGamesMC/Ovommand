<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\exception\ParameterException;
use galaxygames\ovommand\parameter\result\BaseResult;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\ValueResult;
use galaxygames\ovommand\utils\MessageParser;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use shared\galaxygames\ovommand\fetus\IParameter;
use shared\galaxygames\ovommand\fetus\result\IResult;

abstract class BaseParameter implements IParameter{
	protected int $flag = 0;
	// set to true, then params will not give the parsed results but the raw parameters store in Result
	protected bool $returnRaw = false;

	final public function getName() : string{ return $this->name; }
	/**
	 * some parameters have 2 or more spans, but may only require one as those spans can be written in one span! <br>
	 * EG: these input parameters are valid for position: `~~~`, `~~ ~`, `~ ~ ~`
	 */
	public function hasCompactParameter() : bool{ return false; }
	public function getSpanLength() : int{ return 1; }
	public function getFlag() : int{ return $this->flag; }
	public function isReturnRaw() : bool{ return $this->returnRaw; }

	public function __construct(protected string $name, protected bool $optional = false, int $flag = 0){
		$this->setFlag($flag);
	}

	abstract public function getValueName() : string;
	public function isOptional() : bool{ return $this->optional; }

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

	private function setFlag(int $flag) : void{
		$this->flag = match ($flag) {
			0, 1 => $flag,
			default => throw new ParameterException(MessageParser::EXCEPTION_PARAMETER_INVALID_FLAG->translate(['flag' => (string) $flag]), ParameterException::PARAMETER_INVALID_FLAG)
		};
	}

	public function returnRaw(bool $returnRaw = true) : self{
		$this->returnRaw = $returnRaw;
		return $this;
	}

	public function getNetworkParameterData() : CommandParameter{
		return CommandParameter::standard($this->name, $this->getNetworkType()->value(), $this->flag, $this->optional);
	}
}
