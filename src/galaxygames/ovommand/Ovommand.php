<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\exception\CommandException;
use galaxygames\ovommand\exception\ParameterException;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\TextParameter;
use galaxygames\ovommand\utils\SyntaxConst;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use shared\galaxygames\ovommand\fetus\BaseConstraint;
use shared\galaxygames\ovommand\fetus\IOvommand;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

abstract class Ovommand extends Command implements IOvommand{

	/** @var BaseConstraint[] */
	protected array $constraints = [];
	/** @var BaseSubCommand[] */ //bad design
	protected array $subCommands = [];
	/** @var CommandSender */
	protected CommandSender $currentSender;
	/** @var BaseParameter[][] */
	protected array $overloads = [];
	protected int $currentOverloadId = 0;

	public function __construct(string $name, Translatable|string $description = "", ?string $permission = null, Translatable|string|null $usageMessage = null, array $aliases = []){
		parent::__construct($name, $description, "", $aliases);

		$this->setAliases(array_unique($aliases));
		if ($permission !== null) {
			$this->setPermission($permission);
		}
		$this->setup();
		if (empty($this->usageMessage)) {
			$this->setUsage($usageMessage ?? $this->generateUsage());
		}
	}

	protected function generateUsage() : string{
		return "\n- /" . $this->getName() . " " . implode("\n- /" . $this->getName() . " ", $this->generateUsageList());
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
						throw new CommandException("SubCommand with same alias for '$alias' already exists", CommandException::SUB_COMMAND_DUPLICATE_ALIAS);
					}
				}
			} else {
				throw new CommandException("SubCommand with same name for '$subName' already exists", CommandException::SUB_COMMAND_DUPLICATE_ALIAS);
			}
		}
	}

	/**
	 * @return BaseSubCommand[]
	 */
	public function getSubCommands() : array{
		return $this->subCommands;
	}

	public function registerParameters(BaseParameter ...$parameters) : void{
		$hasOptionalParameter = false;
		$hasTextParameter = false;
		foreach ($parameters as $i => $parameter) {
			if ($hasTextParameter) {
				throw new ParameterException("Cannot have more parameters after TextParameter", ParameterException::PARAMETER_AFTER_TEXT_PARAMETER); //TODO: MSG
			}
			if ($parameter instanceof TextParameter) {
				$hasTextParameter = true;
			}
			if ($parameter->isOptional()) {
				$hasOptionalParameter = true;
			} elseif ($hasOptionalParameter) {
				throw new ParameterException("Cannot have non-optional parameters after an optional parameter", ParameterException::PARAMETER_NON_OPTIONAL_AFTER_OPTIONAL); //TODO: MSG
			}
			$this->overloads[$this->currentOverloadId][] = $parameter;
		}
		$this->currentOverloadId++;
	}

	/**
	 * @param string[] $rawParams
	 *
	 * @return array<string, BaseResult>
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

		foreach ($this->overloads as $parameters) {
			$offset = 0;
			$results = [];
			$hasFailed = false;
			$matchPoint = 0;
			foreach ($parameters as $parameter) {
				$span = $parameter->getSpanLength();
				$t = 1;
				do {
					$params = array_slice($rawParams, $offset, $t);
					$result = $parameter->parse($params);
					$results[$parameter->getName()] = $result;
					if ($result instanceof BrokenSyntaxResult && $t !== $span) {
						$t++;
						continue;
					}
					break;
				} while ($t <= $span);
				$offset += $t;
				if ($parameter->hasCompactParameter()) {
					$result->setParsedID($t);
				}
				if ($result instanceof BrokenSyntaxResult) {
					$hasFailed = true;
					$matchPoint += $result->getMatchedParameter();
					break;
				}
				if ($offset === $paramCount + 1 && $parameter->isOptional()) {
					break;
				}
				$matchPoint += $t;
			}
			if (($paramCount > $matchPoint) && !$hasFailed) {
				$results["_error"] = BrokenSyntaxResult::create($rawParams[$matchPoint], implode(" ", $rawParams))
					->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS);
				$hasFailed = true;
			}
			if (!$hasFailed) {
				$successResults[] = $results;
			} else {
				if ($matchPoint > $finalId) {
					$finalId = $matchPoint;
				}
				$failedResults[$matchPoint] = $results;
			}
		}
		// return the failed parse with the most matched semi-parameters, usually the last failed parse.
		if (empty($successResults)) {
			return $failedResults[$finalId];
		}
		return $successResults[array_key_first($successResults)]; // return the first succeed parse.
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

	public function doHandleRawResult() : bool{
		return true;
	}

	/**
	 * @param string[] $args
	 * @param string   $preLabel Return a string combined of its parent-label with the current label
	 */
	final public function execute(CommandSender $sender, string $commandLabel, array $args, string $preLabel = "") : void{
		if (!$this->testPermission($sender)) {
			return;
		}
		foreach ($this->constraints as $constraint) {
			if ($constraint->test($sender, $commandLabel, $args)) {
				$constraint->onSuccess($sender, $commandLabel, $args);
			} else {
				$constraint->onFailure($sender, $commandLabel, $args);
			}
		}
		if (count($args) === 0) {
			$this->onRun($sender, $commandLabel, []);
			return;
		}
		$label = $args[0];
		if ($preLabel === "") {
			$preLabel = $commandLabel;
		} else {
			$preLabel .= " " . $commandLabel;
		}
		if (isset($this->subCommands[$label])) {
			array_shift($args);
			$execute = $this->subCommands[$label];
			if (!$execute->testPermissionSilent($sender)) {
				$msg = $this->getPermissionMessage();
				if ($msg === null) {
					$sender->sendMessage($sender->getServer()->getLanguage()->translateString(TextFormat::RED . "%commands.generic.permission"));
				} elseif (empty($msg)) {
					$sender->sendMessage(str_replace("<permission>", $execute->getPermissions()[0], $msg));
				}
				return;
			}
			$execute->execute($sender, $label, $args, $preLabel);
		} else {
			$passArgs = $this->parseParameters($args);
			$totalPoint = 0;
			foreach ($passArgs as $passArg) {
				if (!$passArg instanceof BrokenSyntaxResult) {
					$preLabel .= " " . implode(" ", array_slice($args, $totalPoint, $passArg->getParsedID()));
				} else {
					$passArg->setPreLabel($preLabel);
				}
				$totalPoint += $passArg->getParsedID();
			}
			$args = array_slice($args, $totalPoint);

			if ($this->onPreRun($sender, $passArgs, $args)) {
				$this->onRun($sender, $commandLabel, $passArgs);
			}
		}
	}

	/**
	 * @return string[]
	 */
	public function generateUsageList() : array{
		$usages = [];
		foreach ($this->subCommands as $k => $subCommand) {
			if ($k === $subCommand->getName()) {
				array_push($usages, ...array_map(static fn(string $in) => $k . " " . $in, $subCommand->generateUsageList()));
			}
		}

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

	/**
	 * Return true will run onRun() and vice versa
	 * @param BaseResult[] $args
	 * @param string[] $nonParsedArgs
	 */
	public function onPreRun(CommandSender $sender, array $args, array $nonParsedArgs) : bool{
		foreach ($args as $arg) {
			if ($arg instanceof BrokenSyntaxResult) {
				$msg = SyntaxConst::parseFromBrokenSyntaxResult($arg, SyntaxConst::SYNTAX_PRINT_OVOMMAND | SyntaxConst::SYNTAX_TRIMMED, $nonParsedArgs);
				if ($msg instanceof Translatable) {
					$msg->prefix(TextFormat::RED);
				} else {
					$msg = TextFormat::RED . $msg;
				}
				$sender->sendMessage($msg);
				$sender->sendMessage("Expect the value to be " . $arg->getExpectedType());
				$sender->sendMessage("Usage: \n" . TextFormat::MINECOIN_GOLD . implode("\n" . TextFormat::MINECOIN_GOLD, explode("\n", $this->getUsage())));
				return false;
			}
		}
		return true;
	}

	/**
	 * @param BaseResult[] $args
	 */
	abstract public function onRun(CommandSender $sender, string $label, array $args, string $preLabel = "") : void;

	abstract protected function setup() : void;

	public function addConstraint(BaseConstraint $constraint) : void{
		$this->constraints[] = $constraint;
	}

	/**
	 * @return BaseConstraint[]
	 */
	public function getConstraints() : array{
		return $this->constraints;
	}

	public function getUsage() : string{
		if (($usage = $this->usageMessage) instanceof Translatable) {
			return $usage->getText();
		}
		return $usage;
	}

	public function getOwningPlugin() : ?Plugin{
		return OvommandHook::getOwnedPlugin();
	}

	public function getCurrentOverloadId() : int{
		return $this->currentOverloadId;
	}
}
