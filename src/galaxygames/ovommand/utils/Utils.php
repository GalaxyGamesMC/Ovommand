<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

use galaxygames\ovommand\BaseCommand;
use function implode;

class Utils{
    public static function parseUsages(BaseCommand $command) : string{
        $usages = ["/" . $command->generateUsageMessage()];
        foreach($command->getSubCommands() as $subCommand) {
            $usages[] = $subCommand->getUsageMessage();
        }
        $usages = array_unique($usages);
        return implode("\n - /" . $command->getName() . " ", $usages);
    }
}
