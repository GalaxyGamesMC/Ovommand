<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\enum\ExceptionMessage;
use galaxygames\ovommand\enum\HardEnum;
use galaxygames\ovommand\enum\SoftEnum;
use galaxygames\ovommand\exception\ParameterOrderException;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\type\ParameterTypes;
use pocketmine\command\CommandSender;

trait ParametableTrait{
    /** @var BaseParameter|SoftEnum|HardEnum[][] */
    protected array $parameters = [];
    protected array $requiredParameterCount = [];

    abstract protected function prepare() : void;

    abstract public function getParameterList() : array;
    abstract public function parseParameters(array $rawArgs, CommandSender $sender) : array;

    public function registerParameter(int $position, BaseParameter|SoftEnum|HardEnum|ParameterTypes $parameter): void {
        if ($position < 0) {
            throw new ParameterOrderException(ExceptionMessage::PARAMETER_NEGATIVE_ORDER->getErrorMessage(["position" => $position]), ParameterOrderException::PARAMETER_NEGATIVE_ORDER_ERROR);
        }
        if ($position > 0 && !isset($this->parameter[$position - 1])) {
            throw new ParameterOrderException(ExceptionMessage::PARAMETER_DETACHED_ORDER->getErrorMessage(["position" => $position]), ParameterOrderException::PARAMETER_DETACHED_ORDER_ERROR);
        }
        foreach ($this->parameter[$position - 1] ?? [] as $para) {
//            if($arg instanceof TextParameter) {
//                throw new ParameterOrderException("No other Parameters can be registered after a TextParameter");
//            }
            if($para->isOptional() && !$parameter->isOptional()){
                throw new ParameterOrderException(ExceptionMessage::PARAMETER_DESTRUCTED_ORDER->getRawErrorMessage(), ParameterOrderException::PARAMETER_DESTRUCTED_ORDER_ERROR);
            }
        }
        $this->parameters[$position][] = $parameter;
        if(!$parameter->isOptional()) {
            $this->requiredParameterCount[$position] = true;
        }
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
