<?php
declare(strict_types = 1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\fetus\IParametable;
use galaxygames\ovommand\fetus\OvommandTrait;
use galaxygames\ovommand\fetus\ParametableTrait;
use pocketmine\command\CommandSender;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\lang\Translatable;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

abstract class BaseSubCommand implements IParametable{
	use OvommandTrait;
	use ParametableTrait;
	protected Translatable|string $usageMessage;

	/** @var string[] */
	private array $hiddenAliases;
	/** @var string[] */
	private array $showAliases;

	/** @var string[] */
	protected array $permissions = [];
	private ?string $permissionMessage = null;

	public function __construct(protected string $name, protected string $description = "", array $hiddenAliases = [], array $showAliases = []){
		$this->hiddenAliases = array_unique($hiddenAliases);
		$this->showAliases = array_unique($showAliases);
			//$this->generateUsageMessage();
		$this->prepare();
	}

	public function isAliases(string $in) : bool{
		return $this->isHiddenAlias($in) || $this->isShowAlias($in);
	}

	public function isHiddenAlias(string $in) : bool{
		return in_array($in, $this->hiddenAliases, true);
	}

	public function isShowAlias(string $in) : bool{
		return in_array($in, $this->showAliases, true);
	}

	public function getName() : string{
		return $this->name;
	}

	public function getDescription() : string{
		return $this->description;
	}

	public function getUsageMessage() : string{
		return $this->usageMessage;
	}

	public function getPermissions() : array{
		return $this->permissions;
	}

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

	public function getOwningPlugin() : Plugin{
		$parent = $this->getParent();
		while ($parent instanceof self) {
			$parent = $parent->getParent();
		}
		return $parent->getOwningPlugin();
	}

	public function getHiddenAliases() : array{
		return $this->hiddenAliases;
	}

	public function getShowAliases() : array{
		return $this->showAliases;
	}

	public function testPermission(CommandSender $target, ?string $permission = null) : bool{
		if($this->testPermissionSilent($target)){
			return true;
		}

		if($this->permissionMessage === null){
			$target->sendMessage(KnownTranslationFactory::pocketmine_command_error_permission($this->name)->prefix(TextFormat::RED));
		}elseif($this->permissionMessage !== ""){
			$target->sendMessage(str_replace("<permission>", $permission ?? implode(";", $this->permissions), $this->permissionMessage));
		}

		return false;
	}
}
