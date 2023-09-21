<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\exception\ParameterException;
use galaxygames\ovommand\fetus\IParametable;
use galaxygames\ovommand\fetus\ParametableTrait;
use galaxygames\ovommand\utils\Utils;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;

use function array_shift;
use function count;
use function str_replace;

abstract class BaseCommand extends Command implements IParametable, PluginOwned{
	use ParametableTrait;

	/** @var CommandSender */
	protected CommandSender $sender;
	/** @var BaseSubCommand[] */
	protected array $subCommands = [];

	protected ?CommandSender $cacheSender = null;
	protected array $constraints;

	public function __construct(protected Plugin $plugin, string $name, Translatable|string $description = "", array $aliases = [], Permission|string|array $permission = null){
		parent::__construct($name, $description, null, $aliases);

		$this->prepare();
		$this->usageMessage = Utils::parseUsages($this);
		if ($permission !== null) {
			$this->setPermission($permission);
		}
	}

	public function registerSubCommand(BaseSubCommand $subCommand) : void{
		if (!isset($this->subCommands[$subName = $subCommand->getName()])) {
			$this->subCommands[$subName] = $subCommand->setParent($this);
			$aliases = $subCommand->getAliases();
			foreach ($aliases as $alias) {
				if (!isset($this->subCommands[$alias])) {
					$this->subCommands[$alias] = $subCommand;
				} else {
					throw new InvalidArgumentException("SubCommand with same alias for '$alias' already exists");
				}
			}
		} else {
			throw new InvalidArgumentException("SubCommand with same name for '$subName' already exists");
		}
	}

	final public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if (!$this->testPermission($sender)) {
			return;
		}
		try {
			$this->cacheSender = $sender;
			/** @var BaseCommand|BaseSubCommand $cmd */
			$execute = $this;
			$passArgs = [];
			if (count($args) > 0) {
				if (isset($this->subCommands[($label = $args[0])])) {
					array_shift($args);
					$execute = $this->subCommands[$label];
					$execute->setCurrentSender($sender);
					if (!$execute->testPermissionSilent($sender)) {
						$msg = $this->getPermissionMessage();
						if ($msg === null) {
							$sender->sendMessage($sender->getServer()->getLanguage()->translateString(TextFormat::RED . "%commands.generic.permission"));
						} elseif (empty($msg)) {
							$sender->sendMessage(str_replace("<permission>", $cmd->getPermissions()[0], $msg));
						}
						return;
					}
				}
				$passArgs = $this->attemptArgumentPassing($execute, $args);
			} elseif ($this->hasRequiredParameters()) {
				throw new ParameterException(); //Todo: shouldn't throw error but error msg?
				//			$this->sendError(self::ERR_INSUFFICIENT_ARGUMENTS);
			}
			if ($passArgs !== null) {
				foreach ($cmd->getConstraints() as $constraint) {
					if (!$constraint->test($sender, $commandLabel, $passArgs)) {
						$constraint->onFailure($sender, $commandLabel, $passArgs);
						return;
					}
				}
				$cmd->onRun($sender, $commandLabel, $passArgs);
			}
		} catch (ParameterException $exception) {

		}
	}

	private function attemptArgumentPassing(IParametable $execute, array $args) : ?array{
		$dat = $execute->parseParameters($args, $this->currentSender);
		if (!empty(($errors = $dat["errors"]))) {
			foreach ($errors as $error) {
				$this->sendError($error["code"], $error["data"]);
			}

			return null;
		}

		return $dat["arguments"];
	}

	/**
	 * @param CommandSender                          $sender
	 * @param string                                 $aliasUsed
	 * @param array|array<string,mixed|array<mixed>> $args
	 */
	abstract public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void;

	protected function sendUsage() : void{
		$this->cacheSender->sendMessage("Usage: " . $this->getUsage());
	}

	public function sendError(int $errorCode, array $args = []) : void{
		$str = $this->errorMessages[$errorCode];
		foreach ($args as $item => $value) {
			$str = str_replace('{' . $item . '}', (string) $value, $str);
		}
		$this->cacheSender->sendMessage($str);
	}

	/**
	 * @return BaseSubCommand[]
	 */
	public function getSubCommands() : array{
		return $this->subCommands;
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

	public function getUsageMessage() : string{
		return $this->getUsage();
	}

	public function getOwningPlugin() : Plugin{
		return $this->plugin;
	}
}
