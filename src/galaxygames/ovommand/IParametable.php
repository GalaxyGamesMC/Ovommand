<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use pocketmine\command\CommandSender;
use galaxygames\ovommand\parameter\BaseParameter;

interface IParametable{
    public function generateUsageMessage() : string;
    public function hasParameters() : bool;

    /**
     * @return BaseParameter[][]
     */
    public function getParameterList(): array;
    public function parseParameters(array $rawArgs, CommandSender $sender) : array;
    public function registerParameters(int $position, BaseParameter $argument) : void;
}
