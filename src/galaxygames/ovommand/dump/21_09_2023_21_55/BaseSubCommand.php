<?php
declare(strict_types = 1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\constraint\BaseConstraint;
use galaxygames\ovommand\fetus\IParametable;
use galaxygames\ovommand\fetus\ParametableTrait;
use pocketmine\command\CommandSender;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\Plugin;

abstract class BaseSubCommand implements IParametable{
	use ParametableTrait;

	/** @var string */
	private string $name;
	/** @var string[] */
	private array $aliases;
	/** @var string */
	private string $description;
	/** @var string */
	protected string $usageMessage;
	/** @var string[] */
	private array $permissions = [];
	/** @var CommandSender */
	protected CommandSender $currentSender;
	/** @var BaseCommand */
	protected BaseCommand $parent;
	/** @var BaseConstraint[] */
	private array $constraints = [];

	public function __construct(string $name, string $description = "", array $aliases = []){
		$this->name = $name;
		$this->description = $description;
		$this->aliases = array_unique($aliases); //TODO: best sol?

		$this->prepare();

		$this->usageMessage = $this->generateUsageMessage();
	}

	public function isAliases(string $in) : bool{
		return in_array($in, $this->aliases, true);
	}

	abstract public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void;

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * @return string[]
	 */
	public function getAliases() : array{
		return $this->aliases;
	}

	/**
	 * @return string
	 */
	public function getDescription() : string{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getUsageMessage() : string{
		return $this->usageMessage;
	}

	/**
	 * @return string[]
	 */
	public function getPermissions() : array{
		return $this->permissions;
	}

	/**
	 * @param array $permissions
	 */
	public function setPermissions(array $permissions) : void{
		$permissionManager = PermissionManager::getInstance();
		foreach ($permissions as $perm) {
			if ($permissionManager->getPermission($perm) === null) {
				throw new \InvalidArgumentException("Cannot use non-existing permission \"$perm\"");
			}
		}
		$this->permissions = $permissions;
	}

	public function setPermission(string $permission) : void{
		$permissionManager = PermissionManager::getInstance();
		if ($permissionManager->getPermission($permission) === null) {
			throw new \InvalidArgumentException("Cannot use non-existing permission \"$permission\"");
		}
		$this->permissions[] = $permission;
	}

	public function testPermissionSilent(CommandSender $sender) : bool{
		foreach ($this->permissions as $permission) {
			if ($sender->hasPermission($permission)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param CommandSender $currentSender
	 *
	 * @internal Used to pass the current sender from the parent command
	 */
	public function setCurrentSender(CommandSender $currentSender) : void{
		$this->currentSender = $currentSender;
	}

	/**
	 *
	 * @internal Used to pass the parent context from the parent command
	 */
	public function setParent(BaseCommand $parent) : self{
		$this->parent = $parent;
		return $this;
	}

	public function sendError(int $errorCode, array $args = []) : void{
		$this->parent->sendError($errorCode, $args);
	}

	public function sendUsage() : void{
		$this->currentSender->sendMessage("/{$this->parent->getName()} $this->usageMessage");
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

	/**
	 * @return Plugin
	 */
	public function getOwningPlugin() : Plugin{
		return $this->parent->getOwningPlugin();
	}
}
