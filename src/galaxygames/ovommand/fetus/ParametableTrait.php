<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\exception\ParameterOrderException;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\result\BaseResult;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;

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
		if ($overloadId < 0) {
			throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_NEGATIVE_ORDER->getErrorMessage(["position" => (string) $overloadId]), ParameterOrderException::PARAMETER_NEGATIVE_ORDER_ERROR);
		}
		if ($overloadId > 0 && !isset($this->parameters[$overloadId - 1])) {
			throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DETACHED_ORDER->getErrorMessage(["position" => (string) $overloadId]), ParameterOrderException::PARAMETER_DETACHED_ORDER_ERROR);
		}
		foreach ($parameters as $parameter) {
			//TODO: TextParameter does not allow
			//TODO: WRONG MSG!!!!!!!!!!!!!!!!!!!!!
			if (!$parameter->isOptional()) {
				foreach ($this->parameters[$overloadId] ?? [] as $para) {
					if ($para->isOptional()) {
						throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DESTRUCTED_ORDER->getRawErrorMessage(), ParameterOrderException::PARAMETER_DESTRUCTED_ORDER_ERROR);
					}
				}
			}

			$this->parameters[$overloadId][] = $parameter;
//			usort($this->parameters, $callback)

			//		usort($this->parameters[$position], static function(BaseParameter $a, BaseParameter $b) : int{
			//			if ($a->getSpanLength() === PHP_INT_MAX) { // if it takes unlimited parameters, pull it down
			//				return 1;
			//			}
			//			return -1;
			//		}); // Sort with their spans
//			usort($this->parameters[$overloadId], static function(BaseParameter $a, BaseParameter $b) : int{
//				return strnatcmp($a->getName() . ": " . $a->getValueName(), $b->getName() . ": " . $b->getValueName());
//			}); // Sort with their alphabet . EDIT: FLAW LOL [not work]
		}
	}

	public function registerParameter(int $overloadId, BaseParameter $parameter) : void{
		$this->registerParameters($overloadId, $parameter);
	}

	public function parseParameters(array $rawParams) : array{
		$paramCount = count($rawParams);
//		print ("paraCount: " . $paramCount . "\n");
		if ($paramCount !== 0 && !$this->hasParameters()) {
			return [];
		}
		$offset = 0;
		$parsed = false;
		$results = [];
		/** @var BaseResult[][] $resultContainers */
		$resultContainers = [];
		$finalId = 0;
		foreach ($this->parameters as $overloadId => $parameters) {
			$offset = 0;
			$results = [];
			foreach ($parameters as $parameter) {
				$params = array_slice($rawParams, $offset, $span = $parameter->getSpanLength());
				echo "\$paramCount = $paramCount \n" .
					"\$offset = $offset \n" .
					"\$span = $span \n" .
					"\$overloadId = $overloadId \n" .
					"\$parameter = " . $parameter->getName() . ": " . $parameter->getValueName() . "\n" .
					"\$params = " . print_r($params, true) . "\n\n"
				;
//				if (empty($params) && $parameter->isOptional()) {
//					echo ("High1.5\n");
//					return $results;
//				}
				if ($offset === $paramCount - $span + 1 && $parameter->isOptional()) {
					echo ("High 1.5\n");
					break;
//					return $results;
				}

				$offset += $span;

				//TODO: Because the parser might choose the wrong overloads, so adding something to stop it would have?

				$result = $parameter->parse($params);
				$results[$parameter->getName()] = $result;
				if ($result instanceof BrokenSyntaxResult && $overloadId + 1 !== count($this->parameters)) {
					echo "$overloadId move\n";
					continue 2;
				}
			}
			if ($paramCount > ($pCount = count($parameters))) {
				$results["_error"] = BrokenSyntaxResult::create(array_slice($rawParams, $pCount, $pCount + 1)[0]);
			}
			$resultContainers[$finalId = $overloadId] = $results;
			echo $overloadId . " passed!\n";
		}
		return $resultContainers[$finalId];
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
