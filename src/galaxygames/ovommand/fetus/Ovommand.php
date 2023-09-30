<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\BaseSubCommand;
use galaxygames\ovommand\constraint\BaseConstraint;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\utils\syntax\SyntaxConst;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\permission\Permission;
use pocketmine\utils\TextFormat;

abstract class Ovommand extends Command implements IParametable{
	use ParametableTrait;

	/** @var BaseConstraint[] */
	protected array $constraints = [];
	/** @var BaseSubCommand[] $subCommands */ //bad design
	protected array $subCommands = [];
	/** @var CommandSender */
	protected CommandSender $currentSender;

	public function __construct(
		string $name, Translatable|string $description = "", Permission|string|array $permission = null,
		Translatable|string|null $usageMessage = null, array $aliases = []
	){
		parent::__construct($name, $description, "", $aliases);

		$this->setAliases(array_unique($aliases));
		if ($permission !== null) {
			$this->setPermission($permission);
		}
		$this->setup();
		$this->setUsage($usageMessage ?? "\n- /" . $this->getName() . " " . implode("\n- /" . $this->getName() . " ", $this->generateUsageList()));
//		var_dump($this->getUsage());
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param array         $args
	 * @param string        $preLabel Return a string combined of its parent labels with the current label
	 *
	 * @return void
	 */
	final public function execute(CommandSender $sender, string $commandLabel, array $args, string $preLabel = "") : void{
		if (!$this->testPermission($sender)) {
			return;
		}
		if (count($args) < 1) {
			$this->onRun($sender, $commandLabel, []);
			return;
		}
		$label = $args[0];
		$preLabel .= $commandLabel . " " . $label;
		$this->setCurrentSender($sender);
		if (isset($this->subCommands[$label])) {
			array_shift($args);
			$execute = $this->subCommands[$label];
			$execute->setCurrentSender($sender);
			if (!$execute->testPermissionSilent($sender)) {
				$msg = $this->getPermissionMessage();
				if ($msg === null) {
					$sender->sendMessage($sender->getServer()->getLanguage()->translateString(TextFormat::RED . "%commands.generic.permission"));
				} elseif (empty($msg)) {
					$sender->sendMessage(str_replace("<permission>", $execute->getPermissions()[0], $msg));
				}
				return;
			}
			$execute->execute($sender, $label, $args, $preLabel . $commandLabel); //TODO: Failed logic?
//			$passArgs = $execute->parseParameters($args);
//			$this->onRun($sender, $commandLabel, $passArgs, $preLabel . $commandLabel);
		} else {
			$passArgs = $this->parseParameters($args);
			if ($this->onSyntaxError($sender, $commandLabel, $passArgs, $args)) {
				$this->onRun($sender, $commandLabel, $passArgs);
			}
		}
	}

	public function generateUsageList() : array{
		$usages = [];
		foreach ($this->subCommands as $k => $subCommand) {
			if ($k !== $subCommand->getName()) {
//				array_push($usages, ...array_map(static fn(string $in) => str_repeat(" ", mb_strlen($subCommand->getName())) . " " . $in, $subCommand->generateUsageList()));
				continue;
			}
			array_push($usages, ...array_map(static fn(string $in) => $k . " " . $in, $subCommand->generateUsageList()));
		} //v1: skip adding alias subcommand's usage

		// v2: adding alias subcommand's usage
		//$mainSub = [];
		//$aliasSub = [];
		//$aliasSubName = [];
		//foreach ($this->subCommands as $k => $subCommand) {
		//	if ($k === $subCommand->getName()) {
		//		$mainSub[$k] = $subCommand->generateUsageList();
		//	} else {
		//		$aliasSubName[$subCommand->getName()][] = $k;
		//		$aliasSub[$subCommand->getName()][] = $subCommand->generateUsageList();
		//	}
		//}
		//$result = [];
		//foreach ($mainSub as $k => $main) {
		//	if (!isset($aliasSubName[$k])) {
		//		continue;
		//	}
		//	$result[$k] = array_map(null, ...$aliasSub[$k]);
		//}
		//foreach ($mainSub as $k => $data) {
		//	foreach ($data as $i => $u) {
		//		$usages[] = $k . " " . $u;
		//		if (!isset($aliasSubName[$k])) {
		//			continue;
		//		}
		//		foreach ($aliasSubName[$k] as $alias) {
		//			$usages[] = TextFormat::GRAY . $alias . " " . $result[$k][$i];
		//		}
		//	}
		//}

		foreach ($this->overloads as $parameters) {
			$param = "";
			foreach ($parameters as $parameter) {
				$hasOptional = $parameter->isOptional();
				$p = $parameter->getName() . ": " . $parameter->getValueName();
				$param .= $hasOptional ? "[$p] " : "<$p> ";
			}
			$usages[] = $param;
		}
		return $usages;
	}

	abstract protected function setup() : void;

	abstract public function onRun(CommandSender $sender, string $label, array $args, string $preLabel = "") : void;

	public function registerSubCommand(BaseSubCommand $subCommand) : void{
		$this->registerSubCommands($subCommand);
	}

	public function registerSubCommands(BaseSubCommand ...$subCommands) : void{
		foreach ($subCommands as $subCommand) {
			if (!isset($this->subCommands[$subName = $subCommand->getName()])) {
				$this->subCommands[$subName] = $subCommand->setParent($this);
				$aliases = [...$subCommand->getShowAliases(), ...$subCommand->getHiddenAliases()];
				foreach ($aliases as $alias) {
					if (!isset($this->subCommands[$alias])) {
						$this->subCommands[$alias] = $subCommand;
					} else {
						throw new \InvalidArgumentException("SubCommand with same alias for '$alias' already exists");
					}
				}
			} else {
				throw new \InvalidArgumentException("SubCommand with same name for '$subName' already exists");
			}
		}
	}

	public function onSyntaxError(CommandSender $sender, string $commandLabel, array $args, array $nonParsedArgs = [], string $preLabel = "") : bool{
		foreach ($args as $arg) {
			if ($arg instanceof BrokenSyntaxResult) {
				array_shift($nonParsedArgs);
				$parts = SyntaxConst::getSyntaxBetweenBrokenPart("/" . $preLabel . " " . implode(" ", $nonParsedArgs), $arg->getBrokenSyntax());
				$sender->sendMessage(TextFormat::RED . SyntaxConst::parseOvommandSyntaxMessage($parts[0], $arg->getBrokenSyntax(), $parts[1]));
				return false;
			}
		}
		return true;
	}

	public function setCurrentSender(CommandSender $currentSender) : Ovommand{
		$this->currentSender = $currentSender;
		return $this;
	}

	public function addConstraint(BaseConstraint $constraint) : void{
		$this->constraints[] = $constraint;
	}

	/**
	 * @return BaseConstraint[]
	 */
	public function getConstraints() : array{
		return $this->constraints;
	}

	public function getSubCommands() : array{
		return $this->subCommands;
	}
}
