<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\exception\CommandException;
use galaxygames\ovommand\exception\ParameterException;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\ParameterTypes;
use galaxygames\ovommand\parameter\result\BaseResult;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\utils\BrokenSyntaxHelper;
use galaxygames\ovommand\utils\Messages;
use galaxygames\ovommand\utils\OvommandHelper;
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
	/** @var Ovommand[] */
	protected array $subCommands = [];
	/** @var BaseParameter[][] */
	protected array $overloads = [];
	private int $currentOverloadId = 0;
	protected bool $doSendingSyntaxWarning = false;
	protected bool $doSendingUsageMessage = false;
	protected bool $doCompactSubCommandAliases = false;

	public function __construct(Translatable|string $description = "", Translatable|string|null $usageMessage = null, ?string $permission = null){
		parent::__construct($description, $usageMessage);
		if ($permission !== null) {
			$this->setPermission($permission);
		}
		$this->setup();
	}

	protected function generateUsage(string $label) : string{
		return Utils::implode($this->generateUsageList(), "\n- /$label ");
	}

	public function registerSubCommand(Ovommand $subCommand, bool $overwrite = false) : void{
		$this->registerSubCommands([$subCommand], $overwrite);
	}

	/**
	 * @param Ovommand[] $subCommands
	 * @param bool $overwrite
	 */
	public function registerSubCommands(array $subCommands, bool $overwrite = false) : void{
		foreach ($subCommands as $name => $subCommand) {
			if ($subCommand === $this) {
				throw new CommandException("Cannot register a subcommand to itself", CommandException::SUB_COMMAND_REGISTER_SELF); // TODO: Proper message
			}
			if (isset($this->subCommands[$name]) && !$overwrite) {
				throw new CommandException(Messages::EXCEPTION_SUBCOMMAND_ALREADY_EXISTED->translate(['name' => $name]));
			}
			$this->subCommands[$name] = $subCommand;
		}
	}

	/** @return Ovommand[] */
	public function getSubCommands() : array{ return $this->subCommands; }

	/**
	 * Registers parameters as an overloading, keeping the input order and enforcing rules: no non-optional after optional,
	 * no parameters after `TextParameter`.
	 * @param BaseParameter ...$parameters
	 */
	public function registerParameters(BaseParameter ...$parameters) : void{
		$hasOptionalParameter = false;
		$hasTextParameter = false;
		foreach ($parameters as $parameter) {
			if ($hasTextParameter) {
				throw new ParameterException(Messages::EXCEPTION_PARAMETER_AFTER_TEXT_PARAMETER->value, ParameterException::PARAMETER_AFTER_TEXT_PARAMETER);
			}
			if ($parameter->getNetworkType() === ParameterTypes::TEXT) {
				$hasTextParameter = true;
			}
			if ($parameter->isOptional()) {
				$hasOptionalParameter = true;
			} elseif ($hasOptionalParameter) {
				throw new ParameterException(Messages::EXCEPTION_PARAMETER_NON_OPTIONAL_AFTER_OPTIONAL->value, ParameterException::PARAMETER_NON_OPTIONAL_AFTER_OPTIONAL);
			}
			$this->overloads[$this->currentOverloadId][] = $parameter;
		}
		$this->currentOverloadId++;
	}

	/**
	 * Parses raw input parameters, validating their format and structure. Returns the first successful match or the most
	 * progressed failed result, while handling optional, compact, and error scenarios.
	 * @param string[] $rawParams
	 * @return array<string, BaseResult>
	 */
	public function parseParameters(array $rawParams) : array{
		$paramCount = count($rawParams);
		if ($paramCount !== 0 && !$this->hasOverloads()) {
			return [];
		}
		/** @var BaseResult[][] $failedResults */
		$failedResults = [];
		$finalId = 0;

		foreach ($this->overloads as $parameters) {
			$offset = 0;
			/** @var BaseResult[] $results */
			$results = [];
			$hasFailed = false;
			$matchPoint = 0;
			foreach ($parameters as $parameter) {
				$span = $parameter->getSpanLength();
				$parameterName = $parameter->getName();
				$parameterAmount = $parameter->hasCompactParameter() ? 1 : $span;
				do {
					$params = array_slice($rawParams, $offset, $parameterAmount);
 					$result = $parameter->parse($params);
					$results[$parameterName] = $result;
					if ($result instanceof BrokenSyntaxResult && $parameterAmount !== $span) {
						$parameterAmount++;
						continue;
					}
					break;
				} while ($parameterAmount <= $span);
				$offset += $parameterAmount;
				if ($parameter->hasCompactParameter()) {
					$result->setParsedPoint($parameterAmount);
				}
				if ($result instanceof BrokenSyntaxResult) {
					$hasFailed = true;
					$matchPoint += 1; // TODO: this 1 is temp, it's wrong btw
					break;
				}
				if ($offset === $paramCount + 1 && $parameter->isOptional()) {
					break;
				}
				$matchPoint += $parameterAmount;
			}
			if (($paramCount > $matchPoint) && !$hasFailed) {
				$hasFailed = true;
				$results["_error"] = BrokenSyntaxResult::create($rawParams[$matchPoint], implode(" ", $rawParams))
					->setCode(BrokenSyntaxResult::CODE_TOO_MUCH_INPUTS);
			}
			if (!$hasFailed) {
				return $results; // return the first success result
			} else {
				if ($matchPoint > $finalId) {
					$finalId = $matchPoint;
				}
				$failedResults[$matchPoint] = $results;
			}
		}
		// return the failed parse with the most matched semi-parameters, usually the last failed parse.
		return $failedResults[$finalId];
	}

	/** @return BaseParameter[][] */
	public function getOverloads() : array{ return $this->overloads; }
	public function hasOverloads() : bool{ return !empty($this->overloads); }

	public function doHandleRawResult() : bool{ return true; }

	/**
	 * @param list<string> $args
	 * @param string $preLabel Return a string combined of its parent-label with the current label
	 */
	final public function execute(CommandSender $sender, string $commandLabel, array $args, string $preLabel = "") : void{
		if (!$this->testPermission($commandLabel, $sender) && !$this->onPermissionRejected($sender)) {
			return;
		}
		foreach ($this->constraints as $constraint) {
			if (!$constraint->constraint($sender, $commandLabel, $args)) {
				$constraint->onFailure($sender, $commandLabel, $args);
				return;
			}
			$constraint->onSuccess($sender, $commandLabel, $args);
		}
		if (empty($args)) {
			if ($this->onPreRun($sender, [])) {
				$this->onRun($sender, $commandLabel, []);
			}
			return;
		}
		$preLabel = $preLabel === "" ? $commandLabel : "$preLabel $commandLabel";
		$label = $args[0];
		if (isset($this->subCommands[$label])) {
			array_shift($args);
			$this->subCommands[$label]->execute($sender, $label, $args, $preLabel);
		} else {
			$parsedArgs = $this->parseParameters($args);
			$totalPoint = 0;
			foreach ($parsedArgs as $passArg) {
				if (!$passArg instanceof BrokenSyntaxResult) {
					$preLabel .= Utils::implode(array_slice($args, $totalPoint, $passArg->getParsedPoint()));
				} else {
//					$passArg->setPreLabel($preLabel);
				}
				$totalPoint += $passArg->getParsedPoint();
			}
			$args = array_slice($args, $totalPoint);

			if ($this->onPreRun($sender, $parsedArgs, $args)) {
				$this->onRun($sender, $commandLabel, $parsedArgs);
			}
		}
	}

	/** @return list<string> */
	public function generateUsageList() : array{
		$usages = [];
		foreach ($this->subCommands as $name => $subCommand) {
			$subCommandUsageList = $subCommand->generateUsageList();
			array_push($usages, ...array_map(static fn(string $input) => "$name $input", $subCommandUsageList));
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
	 * Checks for syntax errors in arguments, sends warnings if enabled, and returns `false` to halt or `true` to proceed.
	 * @param BaseResult[] $args Parsed results
	 * @param list<string> $nonParsedArgs Arguments that hadn't got parsed, mostly due to a failed result from the parsing.
	 */
	public function onPreRun(CommandSender $sender, array $args, array $nonParsedArgs = []) : bool{
		foreach ($args as $arg) {
			if ($arg instanceof BrokenSyntaxResult) {
				$message = BrokenSyntaxHelper::parseFromBrokenSyntaxResult($arg, BrokenSyntaxHelper::SYNTAX_PRINT_OVOMMAND | BrokenSyntaxHelper::SYNTAX_TRIMMED, "bruh");
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

	/** Called when the sender doesn't have the permissions to execute the command / sub commands, return false to confirm the rejection */
	public function onPermissionRejected(CommandSender $sender) : bool{ return false; }

	/** @param BaseResult[] $args */
	abstract public function onRun(CommandSender $sender, string $label, array $args) : void;
	abstract protected function setup() : void;

	public function addConstraint(BaseConstraint $constraint) : void{ $this->constraints[] = $constraint; }
	/** @return BaseConstraint[] */
	public function getConstraints() : array{ return $this->constraints; }

	public function getUsage() : string{
		$usage = parent::getUsage();
		if ($usage instanceof Translatable) {
			return $usage->getText();
		}
		return $usage ?? "";
	}

	public function getOwningPlugin() : Plugin{ return OvommandHook::getOwnedPlugin(); }
	public function getCurrentOverloadId() : int{ return $this->currentOverloadId; }
	public function doSendingSyntaxWarning() : bool{ return $this->doSendingSyntaxWarning; }
	public function doSendingUsageMessage() : bool{ return $this->doSendingUsageMessage; }
	public function doCompactSubCommandAliases() : bool { return $this->doCompactSubCommandAliases; }

	public function setDoSendingSyntaxWarning(bool $doSendingSyntaxWarning = true) : Ovommand{
		$this->doSendingSyntaxWarning = $doSendingSyntaxWarning;
		return $this;
	}

	public function setDoSendingUsageMessage(bool $doSendingUsageMessage = true) : Ovommand{
		$this->doSendingUsageMessage = $doSendingUsageMessage;
		return $this;
	}

	public function setDoCompactSubCommandAliases(bool $doCompactSubCommandAliases = true) : Ovommand {
		$this->doCompactSubCommandAliases = $doCompactSubCommandAliases;
		return $this;
	}

	public function generateOverloads(CommandSender $sender) : array{
		return OvommandHelper::generateOverloads($sender, $this);
	}
}
