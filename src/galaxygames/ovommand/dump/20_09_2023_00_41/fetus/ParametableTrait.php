<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\exception\ParameterOrderException;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use pocketmine\command\CommandSender;

trait ParametableTrait{
	/** @var BaseParameter[][] */
	protected array $parameters = [];
	/** @var bool[] */
	protected array $requiredParameterCount = [];

	abstract protected function prepare() : void;

	abstract public function getParameterList() : array;

	public function validateParameter() : bool{
		if (array_is_list($this->parameters)) {
			return true;
		}
		return false;
	}

	/**
	 * @param BaseParameter[] $parameters
	 *
	 * @return void
	 */
	public function registerParameters(array $parameters) : void{
		$parameters = array_values($parameters);
		$this->parameters[] = $parameters;
	}

	public function registerParameter(int $position, BaseParameter $parameter) : void{
		if ($position < 0) {
			throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_NEGATIVE_ORDER->getErrorMessage(["position" => $position]), ParameterOrderException::PARAMETER_NEGATIVE_ORDER_ERROR);
		}

		if ($position > 0 && !isset($this->parameter[$position - 1])) {
			throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DETACHED_ORDER->getErrorMessage(["position" => $position]), ParameterOrderException::PARAMETER_DETACHED_ORDER_ERROR);
		}
		foreach ($this->parameter[$position - 1] ?? [] as $para) {
			//            if($arg instanceof TextParameter) {
			//                throw new ParameterOrderException("No other Parameters can be registered after a TextParameter");
			//            }
			if ($para->isOptional() && !$parameter->isOptional()) {
				throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DESTRUCTED_ORDER->getRawErrorMessage(), ParameterOrderException::PARAMETER_DESTRUCTED_ORDER_ERROR);
			}
		}
		$this->parameters[$position][] = $parameter;
		usort($this->parameters[$position], static function(BaseParameter $a, BaseParameter $b) : int{
			if ($a->getSpanLength() === PHP_INT_MAX) { // if it takes unlimited parameters, pull it down
				return 1;
			}
			return -1;
		});
		//usort($this->parameters[$position], static function(BaseParameter $a, BaseParameter $b) : int{
		//	return strnatcmp($a->getName() . ": " . $a->getValueName(), $b->getName() . ": " . $b->getValueName());
		//});
		if (!$parameter->isOptional()) {
			$this->requiredParameterCount[$position] = true;
		}
	}

	/**
	 * @param array         $rawParams
	 * @param CommandSender $sender
	 *
	 * @return array
	 */
	public function parseParameters(array $rawParams, CommandSender $sender) : array{
		$paramCount = count($rawParams) - 1;
		if ($paramCount !== 0 && !$this->hasParameters()) {
			return []; //TODO: Better returns type?
		}
		$offset = 1;
		$results = [];
		foreach ($this->parameters as $parameters) {
			$parsed = false;
			$optional = true;
			foreach ($parameters as $parameter) {
				$params = array_slice($rawParams, $offset, $len = $parameter->getSpanLength());
				if (!$parameter->isOptional()) {
					$optional = false; //TODO: COPY CAT :3
				}
				$result = $parameter->parse($params);
				if ($result instanceof BrokenSyntaxResult) {
					$results[$parameter->getName()] = $result;
					return $results; //TODO: how do I know if the parse failed and broken syntax will be echoed :l
				}
				$offset += $len;
				if ($offset > $paramCount) {
					break;
				}
				$results[$parameter->getName()] = $result;
			}
		}
		return $results;
	}

	public function hasRequiredParameters() : bool{
		foreach ($this->parameters as $parameters) {
			foreach ($parameters as $parameter) {
				if (!$parameter->isOptional()) {
					return true;
				}
			}
		}
		return false;
	}

	public function generateUsageMessage() : string{
		$msg = $this->name . " ";
		$params = [];
		foreach ($this->parameters as $parameters) { //TODO: Soft Enum, Hard Enum, etc
			$hasOptional = false;
			$names = [];
			foreach ($parameters as $parameter) {
				$names[] = $parameter->getName() . ": " . $parameter->getValueName();
				if ($parameter->isOptional()) {
					$hasOptional = true;
				}
			}
			$names = implode("|", $names);
			$params[] = $hasOptional ? "[" . $names . "]" : "<" . $names . ">";
		}
		$msg .= implode(" ", $params);

		return $msg;
	}

	public function getParameters() : array{
		return $this->parameters;
	}

	public function hasParameters() : bool{
		return !empty($this->parameters);
	}
}
