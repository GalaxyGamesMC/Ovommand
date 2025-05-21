<?php
declare(strict_types=1);

namespace galaxygames\ovommand\constraint;

use galaxygames\ovommand\utils\Messages;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use shared\galaxygames\ovommand\fetus\BaseConstraint;

class InGameRequiredConstraint extends BaseConstraint{
	public function constraint(CommandSender $sender, string $label, array $args) : bool{
		return $sender instanceof Player;
	}

	public function onFailure(CommandSender $sender, string $label, array $args) : void{
		$sender->sendMessage(TextFormat::RED . Messages::CONSTRAINT_INGAME_FAILURE->value);
	}

	public function onSuccess(CommandSender $sender, string $label, array $args) : void{}
}
