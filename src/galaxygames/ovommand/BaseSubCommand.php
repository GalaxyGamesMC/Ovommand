<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\utils\Utils;
use pocketmine\lang\Translatable;

abstract class BaseSubCommand extends Ovommand{
	/** @var string[] */
	protected array $hiddenAliases;
	/** @var string[] */
	protected array $visibleAliases;
	protected ?Ovommand $parent = null;

	/**
	 * @param list<string> $hiddenAliases
	 * @param list<string> $visibleAliases
	 */
	public function __construct(string $name, protected string|Translatable $description = "", ?string $permission = null, Translatable|string|null $usageMessage = null, array $hiddenAliases = [], array $visibleAliases = []){
		parent::__construct($name, $this->description, $permission, $usageMessage);
		$this->hiddenAliases = array_unique($hiddenAliases);
		$this->visibleAliases = array_unique($visibleAliases);
	}

	public function isAliases(string $input) : bool{
		return $this->isHiddenAlias($input) || $this->isVisibleAlias($input);
	}

	public function isHiddenAlias(string $input) : bool{
		return in_array($input, $this->hiddenAliases, true);
	}

	public function isVisibleAlias(string $input) : bool{
		return in_array($input, $this->visibleAliases, true);
	}

	/** @return list<string> */
	public function getAliases() : array{
		return [...$this->visibleAliases, ...$this->hiddenAliases];
	}

	/** @return list<string> */
	public function getHiddenAliases() : array{
		return $this->hiddenAliases;
	}

	/** @return list<string> */
	public function getVisibleAliases() : array{
		return $this->visibleAliases;
	}

	public function getParent() : ?Ovommand{
		return $this->parent;
	}

	public function setParent(Ovommand $parent) : self{
		$this->parent = $parent;
		$parentHeader = $parent->getName() . " " . $this->getName();
		while ($parent instanceof BaseSubCommand) {
			$newParent = $parent->getParent();
			if ($newParent === null) {
				return $this;
			}
			$parentHeader = $newParent->getName() . " " . $parentHeader;
			$parent = $newParent;
		}
		foreach ($this->getSubCommands() as $subCommand) {
			$subCommand->setParent($this);
		}
		if ($parent instanceof BaseCommand) {
			$this->setUsage(Utils::implode($this->generateUsageList(), "/$parentHeader ", "\n"));
		}
		return $this;
	}
}
