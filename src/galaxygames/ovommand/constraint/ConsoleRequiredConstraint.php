<?php
declare(strict_types=1);

namespace galaxygames\ovommand\constraint;

use galaxygames\ovommand\utils\MessageParser;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use shared\galaxygames\ovommand\fetus\BaseConstraint;

class ConsoleRequiredConstraint extends BaseConstraint{
	public function test(CommandSender $sender, string $aliasUsed, array $args) : bool{
		return $this->isVisibleTo($sender);
	}

	public function onFailure(CommandSender $sender, string $aliasUsed, array $args) : void{
		$sender->sendMessage(TextFormat::RED . MessageParser::CONSTRAINT_CONSOLE_FAILURE->value);
	}

	public function isVisibleTo(CommandSender $sender) : bool{
		return $sender instanceof ConsoleCommandSender;
	}

	public function onSuccess(CommandSender $sender, string $aliasUsed, array $args) : void{}
}
