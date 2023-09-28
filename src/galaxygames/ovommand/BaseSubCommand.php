<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\fetus\Ovommand;
use galaxygames\ovommand\fetus\ParametableTrait;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

abstract class BaseSubCommand extends Ovommand implements PluginOwned{
	use ParametableTrait;
	protected Translatable|string $usageMessage;

	/** @var string[] */
	private array $hiddenAliases;
	/** @var string[] */
	private array $showAliases;

	/** @var string[] */
	protected array $permissions = [];
	private ?string $permissionMessage = null;
	protected Ovommand $parent;

	public function __construct(protected string $name, protected string|Translatable $description = "", array $hiddenAliases = [], array $showAliases = []){
		parent::__construct($name, $this->description);
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

	public function getHiddenAliases() : array{
		return $this->hiddenAliases;
	}

	public function getShowAliases() : array{
		return $this->showAliases;
	}

	public function getParent() : Ovommand{
		return $this->parent;
	}

	/**
	 * @internal Used to pass the parent context from the parent command
	 */
	public function setParent(Ovommand $parent) : self{
		$this->parent = $parent;
		return $this;
	}

	public function getOwningPlugin() : Plugin{
		$parent = $this->getParent();
		while ($parent instanceof BaseSubCommand) {
			$parent = $parent->getParent();
		}
		return $parent->getOwningPlugin();
	}
}
