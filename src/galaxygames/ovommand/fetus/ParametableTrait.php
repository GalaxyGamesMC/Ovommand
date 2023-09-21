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

	public function validateParameter() : bool{
		if (array_is_list($this->parameters)) {
			return true;
		}
		return false;
	}

	public function registerParameters(int $overloadId, BaseParameter ...$parameters) : void{
		foreach ($parameters as $parameter) {
			if ($overloadId < 0) {
				throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_NEGATIVE_ORDER->getErrorMessage(["position" => $overloadId]), ParameterOrderException::PARAMETER_NEGATIVE_ORDER_ERROR);
			}
			//TODO: TextParameter does not allow
			//TODO: WRONG MSG!!!!!!!!!!!!!!!!!!!!!
			if ($overloadId > 0 && !isset($this->parameter[$overloadId - 1])) {
				throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DETACHED_ORDER->getErrorMessage(["position" => $overloadId]), ParameterOrderException::PARAMETER_DETACHED_ORDER_ERROR);
			}
			if (!$parameter->isOptional()) {
				foreach ($this->parameter[$overloadId] ?? [] as $para) {
					if ($para->isOptional()) {
						throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DESTRUCTED_ORDER->getRawErrorMessage(), ParameterOrderException::PARAMETER_DESTRUCTED_ORDER_ERROR);
					}
				}
			}

			$this->parameters[$overloadId][] = $parameter;
			if (!$parameter->isOptional()) {
				$this->requiredParameterCount[$overloadId] = true; //TODO: HELP, I DUNNO!
			}

			//		usort($this->parameters[$position], static function(BaseParameter $a, BaseParameter $b) : int{
			//			if ($a->getSpanLength() === PHP_INT_MAX) { // if it takes unlimited parameters, pull it down
			//				return 1;
			//			}
			//			return -1;
			//		});
			//		usort($this->parameters[$position], static function(BaseParameter $a, BaseParameter $b) : int{
			//			return strnatcmp($a->getName() . ": " . $a->getValueName(), $b->getName() . ": " . $b->getValueName());
			//		});
		}
	}

	public function registerParameter(int $overloadId, BaseParameter $parameter) : void{
		$this->registerParameters($overloadId, $parameter);
	}

	public function parseParameters(array $rawParams, CommandSender $sender, string $commandLabel) : array{
		$paramCount = count($rawParams);
		if ($paramCount !== 0 && !$this->hasParameters()) {
			return []; //TODO: Better returns type?
		}
		$offset = 0;
		$results = [];
		foreach ($this->parameters as $overloadId => $parameters) {
			$parsed = false;
			$optional = true;
			foreach ($parameters as $parameter) {
				$params = array_slice($rawParams, $offset, $len = $parameter->getSpanLength());
				if (!$parameter->isOptional()) {
					$optional = false; //TODO: COPY CAT :3
				}
				$result = $parameter->parse($params);
				if ($result instanceof BrokenSyntaxResult) {
					$results = [];
					break;
				}
				$offset += $len;
				$parsed = true;
				$results[$parameter->getName()] = $result;

				if ($offset > $paramCount) {
					break;
				}
			}
			if (!$parsed && !($optional && $paramCount === 0)) {
				return [];
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
		foreach ($this->parameters as $parameters) {
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

	/**
	 * @return BaseParameter[][]
	 */
	public function getParameterList() : array{
		return $this->parameters;
	}

	public function hasParameters() : bool{
		return !empty($this->parameters);
	}
}
