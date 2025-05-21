<?php
declare(strict_types=1);
namespace galaxygames\ovommand\utils;

use galaxygames\ovommand\Ovommand;
use galaxygames\ovommand\parameter\BaseParameter;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandOverload;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

class OvommandHelper{
	/** @return CommandOverload[] */
	public static function generateOverloads(CommandSender $sender, Ovommand $command) : array{
		$overloads = [];
		foreach ($command->getSubCommands() as $label => $subCommand) {
			if (!$subCommand->testPermissionSilent($sender)) {
				continue;
			}
			foreach ($subCommand->getConstraints() as $constraint) {
				if (!$constraint->constraint($sender, $label, [])) {
					continue 2;
				}
			}
			$scParam = CommandParameter::enum($label, new CommandEnum("scmd#" . spl_object_id($subCommand), [$label]), 1);
			$overloadList = self::generateOverloads($sender, $subCommand);
			if (empty($overloadList)) {
				$overloads[] = new CommandOverload(false, [$scParam]);
			} else {
				foreach ($overloadList as $overload) {
					$overloads[] = new CommandOverload(false, [$scParam, ...$overload->getParameters()]);
				}
			}
		}
		foreach ($command->getOverloads() as $parameters) {
			$overloads[] =  new CommandOverload(false, array_map(static fn(BaseParameter $parameter) : CommandParameter => $parameter->getNetworkParameterData(), $parameters));
		}
		return $overloads;
	}
}

//use galaxygames\ovommand\BaseCommand;
//use galaxygames\ovommand\BaseSubCommand;
//use galaxygames\ovommand\Ovommand;
//use galaxygames\ovommand\parameter\default\IntParameter;
//
//class OvommandTreeOverload{
//
//}
//
//class OvommandTree{
//	public readonly BaseCommand|BaseSubCommand $command;
//	/** @var OvommandTree */
//	public readonly array $children;
//	public function __construct(){
//
//	}
//
//	public static function create() : self{
//		return new OvommandTree();
//	}
//
//	public function int(string $name) : self{
//		$this->command->registerParameters(new IntParameter($name));
//		return $this;
//	}
//
//	public function float(string $name) : self{
//		return $this;
//	}
//
//	public function string(string $name) : self{
//		return $this;
//	}
//
//	public function position(string $name) : self{
//		return $this;
//	}
//
//	public function blockPosition(string $name) : self{
//		return $this;
//	}
//
//	public function target(string $name) : self{
//		return $this;
//	}
//}
//
//class OvommandHelper{
//	public static function createCommand() : Ovommand{
//	}
//}