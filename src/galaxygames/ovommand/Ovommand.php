<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\exception\ParameterOrderException;
use galaxygames\ovommand\exception\SubCommandException;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\utils\syntax\SyntaxConst;
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
	/** @var BaseSubCommand[] $subCommands */ //bad design
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
		$this->setUsage($usageMessage ?? $this->generateUsage());
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
						throw new SubCommandException("SubCommand with same alias for '$alias' already exists", SubCommandException::SUB_COMMAND_DUPLICATE_ALIAS_ERROR);
					}
				}
			} else {
				throw new SubCommandException("SubCommand with same name for '$subName' already exists", SubCommandException::SUB_COMMAND_DUPLICATE_ALIAS_ERROR);
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
		foreach ($parameters as $parameter) {
			if ($parameter->isOptional()) {
				$hasOptionalParameter = true;
			} elseif ($hasOptionalParameter) {
				throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DESTRUCTED_ORDER->getRawErrorMessage(), ParameterOrderException::PARAMETER_DESTRUCTED_ORDER_ERROR);
			}

			$this->overloads[$this->currentOverloadId++][] = $parameter;
		}
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
			$totalSpan = 0;
			$matchPoint = 0;
			foreach ($parameters as $parameter) {
				$span = $parameter->getSpanLength();
				if ($offset === $paramCount - $span + 1 && $parameter->isOptional()) {
					break;
				}
				$params = array_slice($rawParams, $offset, $span);
				$totalSpan += $span;

				//				if (($pCount = count($params)) < $parameter->getSpanLength()) {
				//					$results["_" . $parameter->getName()] = BrokenSyntaxResult::create("", expectedType: $parameter->getValueName());
				//					break;
				//				} temp?,
				//this got replaced by BrokenSyntaxResult::setMatchedParameter()...
				//TODO: gotta find a better name for that function

				$offset += $span;
				$result = $parameter->parse($params);
				$results[$parameter->getName()] = $result;
				if ($result instanceof BrokenSyntaxResult) {
					$hasFailed = true;
					$matchPoint += $result->getMatchedParameter();
					break;
				}
				$matchPoint += $span;
			}
			if (($paramCount > $totalSpan) && !$hasFailed) {
				$results["_error"] = BrokenSyntaxResult::create($rawParams[$totalSpan], implode(" ", $rawParams))->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS);
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
		if (empty($successResults)) {
			return $failedResults[$finalId];
		}
		return $successResults[array_key_first($successResults)];
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

	/**
	 * @param string[] $args
	 * @param string   $preLabel Return a string combined of its parent labels with the current label
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
		if (count($args) < 1) {
			$this->onRun($sender, $commandLabel, []);
			return;
		}
		$label = $args[0];
		if ($preLabel === "") {
			$preLabel = $commandLabel;
		} else {
			$preLabel .= " " . $commandLabel;
		}
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
			$execute->execute($sender, $label, $args, $preLabel);
		} else {
			$passArgs = $this->parseParameters($args);
			if ($this->onSyntaxError($sender, $commandLabel, $passArgs, $args, $preLabel)) {
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
	 * @param BaseResult[] $args
	 * @param string[]     $nonParsedArgs
	 */
	public function onSyntaxError(CommandSender $sender, string $commandLabel, array $args, array $nonParsedArgs = [], string $preLabel = "") : bool{
		foreach ($args as $arg) {
			if ($arg instanceof BrokenSyntaxResult) {
				for ($i = 0; $i <= $arg->getMatchedParameter(); ++$i) {
					array_shift($nonParsedArgs);
				}
				$arg->setPreLabel($preLabel);
				$fullCMD = "/" . $preLabel . " " . $arg->getFullSyntax() . " " . implode(" ", $nonParsedArgs);
				$parts = SyntaxConst::getSyntaxBetweenBrokenPart($fullCMD, $arg->getBrokenSyntax());

				$msg = match ($arg->getCode()) {
					default => SyntaxConst::parseOvommandSyntaxMessage($parts[0], $arg->getBrokenSyntax(), $parts[1]),
					BrokenSyntaxResult::CODE_NOT_ENOUGH_INPUTS => SyntaxConst::parseOvommandSyntaxMessage($fullCMD, "", "")
				};

				$sender->sendMessage(TextFormat::RED . $msg);
				$sender->sendMessage("Expect the value is " . $arg->getExpectedType());
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

	public function setCurrentSender(CommandSender $currentSender) : Ovommand{
		$this->currentSender = $currentSender;
		return $this;
	}

	public function getCurrentSender() : CommandSender{
		return $this->currentSender;
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
