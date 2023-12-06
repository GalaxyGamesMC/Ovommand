<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\exception\CommandException;
use galaxygames\ovommand\exception\ParameterException;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\BaseResult;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\TextParameter;
use galaxygames\ovommand\utils\BrokenSyntaxParser;
use galaxygames\ovommand\utils\MessageParser;
use galaxygames\ovommand\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use shared\galaxygames\ovommand\fetus\BaseConstraint;
use shared\galaxygames\ovommand\fetus\IOvommand;

abstract class Ovommand extends Command implements IOvommand{
	/** @var BaseConstraint[] */
	protected array $constraints = [];
	/** @var BaseSubCommand[] */
	protected array $subCommands = [];
	/** @var BaseParameter[][] */
	protected array $overloads = [];
	protected int $currentOverloadId = 0;
	protected bool $doSendingSyntaxWarning = false;
	protected bool $doSendingUsageMessage = false;

	public function __construct(string $name, Translatable|string $description = "", ?string $permission = null, Translatable|string|null $usageMessage = null, array $aliases = []){
		parent::__construct($name, $description, "", $aliases);

		$this->setAliases(array_unique($aliases));
		if ($permission !== null) {
			$this->setPermission($permission);
		}
		$this->setup();
		if ($this->usageMessage === "") {
			$this->setUsage($usageMessage ?? $this->generateUsage());
		}
	}

	protected function generateUsage() : string{
		return Utils::implode($this->generateUsageList(), "\n- /" . $this->getName() . " ");
	}

	public function registerSubCommands(BaseSubCommand ...$subCommands) : void{
		foreach ($subCommands as $subCommand) {
			$subName = $subCommand->getName();
			if (!isset($this->subCommands[$subName])) {
				$this->subCommands[$subName] = $subCommand->setParent($this);
				$aliases = [...$subCommand->getShowAliases(), ...$subCommand->getHiddenAliases()];
				foreach ($aliases as $alias) {
					if (!isset($this->subCommands[$alias])) {
						$this->subCommands[$alias] = $subCommand;
					} else {
						throw new CommandException(MessageParser::EXCEPTION_SUB_COMMAND_DUPLICATE_ALIAS->translate(["alias" => $alias]), CommandException::SUB_COMMAND_DUPLICATE_ALIAS);
					}
				}
			} else {
				throw new CommandException(MessageParser::EXCEPTION_SUB_COMMAND_DUPLICATE_NAME->translate(["subName" => $subName]), CommandException::SUB_COMMAND_DUPLICATE_NAME);
			}
		}
	}

	/** @return BaseSubCommand[] */
	public function getSubCommands() : array{
		return $this->subCommands;
	}

	public function registerParameters(BaseParameter ...$parameters) : void{
		$hasOptionalParameter = false;
		$hasTextParameter = false;
		foreach ($parameters as $parameter) {
			if ($hasTextParameter) {
				throw new ParameterException(MessageParser::EXCEPTION_PARAMETER_AFTER_TEXT_PARAMETER->value, ParameterException::PARAMETER_AFTER_TEXT_PARAMETER);
			}
			if ($parameter instanceof TextParameter) {
				$hasTextParameter = true;
			}
			if ($parameter->isOptional()) {
				$hasOptionalParameter = true;
			} elseif ($hasOptionalParameter) {
				throw new ParameterException(MessageParser::EXCEPTION_PARAMETER_NON_OPTIONAL_AFTER_OPTIONAL->value, ParameterException::PARAMETER_NON_OPTIONAL_AFTER_OPTIONAL);
			}
			$this->overloads[$this->currentOverloadId][] = $parameter;
		}
		$this->currentOverloadId++;
	}

	/**
	 * Parse the parameters
	 * @param string[] $rawParams
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
				$p = 1;
				do {
					$params = array_slice($rawParams, $offset, $p);
 					$result = $parameter->parse($params);
					$results[$parameter->getName()] = $result;
					if ($result instanceof BrokenSyntaxResult && $p !== $span) {
						$p++;
						continue;
					}
					break;
				} while ($p <= $span);
				$offset += $p;
				if ($parameter->hasCompactParameter()) {
					$result->setParsedPoint($p);
				}
				if ($result instanceof BrokenSyntaxResult) {
					$hasFailed = true;
					$matchPoint += $result->getMatchedParameter();
					break;
				}
				if ($offset === $paramCount + 1 && $parameter->isOptional()) {
					break;
				}
				$matchPoint += $p;
			}
			if (($paramCount > $matchPoint) && !$hasFailed) {
				$hasFailed = true;
				$results["_error"] = BrokenSyntaxResult::create($rawParams[$matchPoint], implode(" ", $rawParams))
					->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS);
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
		if (count($successResults) === 0) {
			return $failedResults[$finalId];
		}
		return $successResults[array_key_first($successResults)]; // return the first succeed parse.
	}

	/** @return BaseParameter[][] */
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
	 * @param string $preLabel Return a string combined of its parent-label with the current label
	 */
	final public function execute(CommandSender $sender, string $commandLabel, array $args, string $preLabel = "") : void{
		if (!$this->testPermission($sender) && !$this->onPermissionRejected($sender)) {
			return;
		}
		foreach ($this->constraints as $constraint) {
			if ($constraint->test($sender, $commandLabel, $args)) {
				$constraint->onSuccess($sender, $commandLabel, $args);
			} else {
				$constraint->onFailure($sender, $commandLabel, $args);
				return;
			}
		}
		if (count($args) === 0) {
			if ($this->onPreRun($sender, [])) {
				$this->onRun($sender, $commandLabel);
			}
			return;
		}
		$preLabel === "" ? $preLabel = $commandLabel : $preLabel .= " " . $commandLabel;
		$label = $args[0];
		if (isset($this->subCommands[$label])) {
			$execute = $this->subCommands[$label];
			array_shift($args);
			$execute->execute($sender, $label, $args, $preLabel);
		} else {
			$passArgs = $this->parseParameters($args);
			$totalPoint = 0;
			foreach ($passArgs as $passArg) {
				if (!$passArg instanceof BrokenSyntaxResult) {
					$preLabel .= Utils::implode(array_slice($args, $totalPoint, $passArg->getParsedPoint()), " ");
				} else {
					$passArg->setPreLabel($preLabel);
				}
				$totalPoint += $passArg->getParsedPoint();
			}
			$args = array_slice($args, $totalPoint);

			if ($this->onPreRun($sender, $passArgs, $args)) {
				$this->onRun($sender, $commandLabel, $passArgs);
			}
		}
	}

	/** @return string[] */
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
	public function onPreRun(CommandSender $sender, array $args, array $nonParsedArgs = []) : bool{
		foreach ($args as $arg) {
			if ($arg instanceof BrokenSyntaxResult) {
				$message = BrokenSyntaxParser::parseFromBrokenSyntaxResult($arg, BrokenSyntaxParser::SYNTAX_PRINT_OVOMMAND | BrokenSyntaxParser::SYNTAX_TRIMMED, $nonParsedArgs);
				$message instanceof Translatable ? $message->prefix(TextFormat::RED) : $message = TextFormat::RED . $message;
				if ($this->doSendingSyntaxWarning) {
					$sender->sendMessage($message);
				}
				if ($this->doSendingUsageMessage) {
					$sender->sendMessage("Usage: \n" . TextFormat::MINECOIN_GOLD . implode("\n" . TextFormat::MINECOIN_GOLD, explode("\n", $this->getUsage())));
				}
				return false;
			}
		}
		return true;
	}

	/** Called when the sender don't have the permissions to execute the command / sub commands, return false to confirm the rejection */
	public function onPermissionRejected(CommandSender $sender) : bool{
		return false;
	}

	/** @param BaseResult[] $args */
	abstract public function onRun(CommandSender $sender, string $label, array $args = []) : void;

	abstract protected function setup() : void;

	public function addConstraint(BaseConstraint $constraint) : void{
		$this->constraints[] = $constraint;
	}

	/** @return BaseConstraint[] */
	public function getConstraints() : array{
		return $this->constraints;
	}

	public function getUsage() : string{
		$usage = $this->usageMessage;
		if ($usage instanceof Translatable) {
			return $usage->getText();
		}
		return $usage;
	}

	public function getOwningPlugin() : Plugin{
		return OvommandHook::getOwnedPlugin();
	}

	public function getCurrentOverloadId() : int{
		return $this->currentOverloadId;
	}

	public function doSendingSyntaxWarning() : bool{
		return $this->doSendingSyntaxWarning;
	}

	public function isDoSendingUsageMessage() : bool{
		return $this->doSendingUsageMessage;
	}

	public function setDoSendingSyntaxWarning(bool $doSendingSyntaxWarning = true) : Ovommand{
		$this->doSendingSyntaxWarning = $doSendingSyntaxWarning;
		return $this;
	}

	public function setDoSendingUsageMessage(bool $doSendingUsageMessage = true) : Ovommand{
		$this->doSendingUsageMessage = $doSendingUsageMessage;
		return $this;
	}
}
