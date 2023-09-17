<?php
declare(strict_types=1);

namespace galaxygames\ovommand\constraint;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class InGameRequiredConstraint extends BaseConstraint {

    public function test(CommandSender $sender, string $aliasUsed, array $args): bool {
        return $this->isVisibleTo($sender);
    }

    public function onFailure(CommandSender $sender, string $aliasUsed, array $args): void {
        $sender->sendMessage(TextFormat::RED . "This command must be executed in-game.");
    }

    public function isVisibleTo(CommandSender $sender): bool {
		return $sender instanceof Player;
	}
}