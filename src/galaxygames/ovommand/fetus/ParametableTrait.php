<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\exception\ParameterOrderException;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use shared\galaxygames\ovommand\fetus\BaseResult;

trait ParametableTrait{
	/** @var BaseParameter[][] */
	protected array $overloads = [];

	public function registerParameters(int $overloadId, BaseParameter ...$parameters) : void{
		if ($overloadId < 0) {
			throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_NEGATIVE_ORDER->getErrorMessage(["position" => (string) $overloadId]), ParameterOrderException::PARAMETER_NEGATIVE_ORDER_ERROR);
		}
		if ($overloadId > 0 && !isset($this->overloads[$overloadId - 1])) {
			throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DETACHED_ORDER->getErrorMessage(["position" => (string) $overloadId]), ParameterOrderException::PARAMETER_DETACHED_ORDER_ERROR);
		}
		foreach ($parameters as $parameter) {
			if (!$parameter->isOptional()) {
				foreach ($this->overloads[$overloadId] ?? [] as $para) {
					if ($para->isOptional()) {
						throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DESTRUCTED_ORDER->getRawErrorMessage(), ParameterOrderException::PARAMETER_DESTRUCTED_ORDER_ERROR);
					}
				}
			}

			$this->overloads[$overloadId][] = $parameter;
		}
	}

	public function registerParameter(int $overloadId, BaseParameter $parameter) : void{
		$this->registerParameters($overloadId, $parameter);
	}

	/**
	 * @param string[] $rawParams
	 *
	 * @return BaseResult[]
	 */
	public function parseParameters(array $rawParams) : array{
		$paramCount = count($rawParams);
		if ($paramCount !== 0 && !$this->hasOverloads()) {
			return [];
		}
		/** @var BaseResult[][] $successResults */
		$successResults = [];
		/** @var BaseResult[][] $failedResults */
		$failedResults = [];
		$finalId = 0;
		
//		$spanMap = array_map(static fn(array $parameters) => array_map(static fn(BaseParameter $parameter) => $parameter->getSpanLength(), $parameters), $this->overloads);
//		var_dump($spanMap);
		
		foreach ($this->overloads as $overloadId => $parameters) {
			$offset = 0;
			$results = [];
			$hasFailed = false;
			$totalSpan = 0;
			$matchPoint = 0;
			foreach ($parameters as $parameterId => $parameter) {
				$span = $parameter->getSpanLength();
				if ($offset === $paramCount - $span + 1 && $parameter->isOptional()) {
					break;
				}
				$params = array_slice($rawParams, $offset, $span);
				$totalSpan += $span;

//				if (($pCount = count($params)) < $parameter->getSpanLength()) {
//					$results["_" . $parameter->getName()] = BrokenSyntaxResult::create("", expectedType: $parameter->getValueName());
//					break;
//				}
				$offset += $span;
				$result = $parameter->parse($params);
				$results[$parameter->getName()] = $result;
				if ($result instanceof BrokenSyntaxResult) {
					$hasFailed = true;
					$matchPoint += $result->getMatchedParameter();
					break;
				}
				$matchPoint += $span;
			}
			if ($paramCount > $totalSpan && !$hasFailed) {
				$results["_error"] = BrokenSyntaxResult::create("", implode(" ", $rawParams));
				$hasFailed = true;
			}
			echo "Max point of " . $overloadId . " is " . $matchPoint . "\n";
			if (!$hasFailed) {
				$successResults[] = $results;
			} else {
				if ($matchPoint > $finalId) {
					$finalId = $matchPoint;
				}
				$failedResults[$matchPoint] = $results;
			}
		}
		if (empty($successResults)) {
			return $failedResults[$finalId];
		}
		return $successResults[array_key_first($successResults)];
	}

	/**
	 * @return BaseParameter[][]
	 */
	public function getOverloads() : array{
		return $this->overloads;
	}

	public function hasOverloads() : bool{
		return !empty($this->overloads);
	}
}
