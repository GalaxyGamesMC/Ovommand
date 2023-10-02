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
				$params = array_slice($rawParams, $offset, $span = $parameter->getSpanLength());
//				echo "OPEN\n";
//				var_dump($params);
//				echo "CLOSE\n";
				$totalSpan += $span;
				if ($offset === $paramCount - $span + 1 && $parameter->isOptional()) {
//					echo "CLOSE1\n\n";
					break;
				}
				if (($pCount = count($params)) < $parameter->getSpanLength()) {
					$results[$parameter->getName()] = BrokenSyntaxResult::create($params[$span - $offset] ?? "", expectedType: $parameter->getValueName());
//					echo "CLOSE2\n\n";
					break;
				}
				$offset += $span;
				//TODO: Because the parser might choose the wrong overloads, so adding something to stop it from doing that?
				$result = $parameter->parse($params);
				$results[$parameter->getName()] = $result;
//				if ($result instanceof BrokenSyntaxResult && $overloadId + 1 !== count($this->overloads)) {
				if ($result instanceof BrokenSyntaxResult) {
					$hasFailed = true;
//					echo "CLOSE2.5\n\n";
					break;
				}
				$matchPoint+= $span;
			}
			if ($paramCount > $totalSpan) {
				$results["_error"] = BrokenSyntaxResult::create("", implode(" ", $rawParams));
//				echo "CLOSE3\n\n";
				$hasFailed = true;
			}
//			echo "Max point of " . $overloadId . " is " . $matchPoint . "\n";
			if (!$hasFailed) {
				$successResults[] = $results;
			} else {
				if ($matchPoint > $finalId) {
					$finalId = $matchPoint;
				}
				$failedResults[$matchPoint] = $results;
			}
		}
//		var_dump("Success", $successResults);
//		var_dump("Fail", $failedResults);
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
