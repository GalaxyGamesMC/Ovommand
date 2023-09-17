<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus\beta;

use galaxygames\ovommand\enum\ExceptionMessage;
use galaxygames\ovommand\enum\HardEnum;
use galaxygames\ovommand\enum\SoftEnum;
use galaxygames\ovommand\exception\ParameterOrderException;
use galaxygames\ovommand\fetus\BaseCommand;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\type\ParameterTypes;
use pocketmine\command\CommandSender;

trait ParametableTrait{
    /** @var BaseParameter[] */
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

    public function registerParameter(int $position, BaseParameter $parameter) : void {
        if ($position < 0) {
            throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_NEGATIVE_ORDER->getErrorMessage(["position" => $position]), ParameterOrderException::PARAMETER_NEGATIVE_ORDER_ERROR);
        }
        if ($position > 0 && !isset($this->parameter[$position - 1])) {
            throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DETACHED_ORDER->getErrorMessage(["position" => $position]), ParameterOrderException::PARAMETER_DETACHED_ORDER_ERROR);
        }
        foreach ($this->parameter[$position - 1] ?? [] as $para) {
            if($para->isOptional() && !$parameter->isOptional()){
                throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DESTRUCTED_ORDER->getRawErrorMessage(), ParameterOrderException::PARAMETER_DESTRUCTED_ORDER_ERROR);
            }
        }
        $this->parameters[$position] = $parameter;
        if(!$parameter->isOptional()) {
            $this->requiredParameterCount[$position] = true;
        }
    }

    public function parseParameters(array $rawParams, CommandSender $sender) : array{
        $paramCount = count($rawParams);
        $return = [
            "parameters" => [],
            "errors" => []
        ];
        // try parsing parameters
        $required = count($this->requiredParameterCount);
        if($paramCount !== 0 && !$this->hasParameters()) {
            return $return;
        }
        $offset = 0;
        foreach($this->parameters as $position => $parameters) {
            // try the one that spans more first... before the others
            usort($parameters, static function (BaseArgument $a, BaseArgument $b): int {
                if($a->getSpanLength() === PHP_INT_MAX) { // if it takes unlimited parameters, pull it down
                    return 1;
                }
                return -1;
            });
            $parsed = false;
            $optional = true;
            foreach($parameters as $parameter) {
                $arg = trim(implode(" ", array_slice($rawParams, $offset, ($len = $parameter->getSpanLength()))));
                if(!$parameter->isOptional()) {
                    $optional = false;
                }
                if($arg !== "" && $parameter->canParse($arg, $sender)) {
                    $k = $parameter->getName();
                    $result = (clone $parameter)->parse($arg, $sender);
                    if(isset($return["parameters"][$k]) && !is_array($return["parameters"][$k])) {
                        $old = $return["parameters"][$k];
                        unset($return["parameters"][$k]);
                        $return["parameters"][$k] = [$old];
                        $return["parameters"][$k][] = $result;
                    } else {
                        $return["parameters"][$k] = $result;
                    }
                    if(!$optional) {
                        $required--;
                    }
                    $offset += $len;
                    $parsed = true;
                    break;
                }
                if($offset > $paramCount) {
                    break; // we've reached the end of the argument list the user passed
                }
            }
            if(!$parsed && !($optional && empty($arg))) { // we tried every other possible argument type, none was satisfied
                $return["errors"][] = [
                    "code" => BaseCommand::ERR_INVALID_ARG_VALUE,
                    "data" => [
                        "value" => $rawArgs[$offset] ?? "",
                        "position" => $pos + 1
                    ]
                ];

                return $return; // let's break it here.
            }
        }
        if($offset < count($rawArgs)) { // this means that the parameters our user sent is more than the needed amount
            $return["errors"][] = [
                "code" => BaseCommand::ERR_TOO_MANY_parameters,
                "data" => []
            ];
        }
        if($required > 0) {// We still have more unfilled required parameters
            $return["errors"][] = [
                "code" => BaseCommand::ERR_INSUFFICIENT_parameters,
                "data" => []
            ];
        }

        return $return;
    }

    public function hasRequiredParameters() : bool{
        foreach($this->parameters as $parameters) {
            foreach($parameters as $parameter) {
                if(!$parameter->isOptional()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function generateUsageMessage(): string {
        $msg = $this->name . " ";
        $params = [];
        foreach ($this->parameters as $parameters) { //TODO: Soft Enum, Hard Enum, etc
            $hasOptional = false;
            $names = [];
            foreach ($parameters as $parameter) {
                $names[] = $parameter->getName() . ":" . $parameter->getTypeName();
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

    public function getParameters(): array {
        return $this->parameters;
    }

    public function hasParameters() : bool{
        return !empty($this->parameters);
    }
}
