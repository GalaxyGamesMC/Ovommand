<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use shared\galaxygames\ovommand\exception\OvommandEnumPoolException;

abstract class OvommandEnum implements IEnum{
	protected string $name;
	/** @var array<string, mixed> */
	protected array $values;
	/** @var string[] */
	protected array $hiddenAliases = [];
	/** @var string[] */
	protected array $showAliases = [];
	protected bool $isDefault = false;
	protected bool $isProtected = false;

	public function isProtected() : bool{
		return $this->isProtected;
	}

	public function asProtected() : ProtectedEnum{
		return new ProtectedEnum($this);
	}

	final public function getName() : string{
		return $this->name;
	}

	final public function isDefault() : bool{
		return $this->isDefault;
	}

	abstract public function encode() : CommandEnum;
	abstract public function getValue(string $key) : mixed;

	final public function isSoft() : bool{
		return match(true) {
			$this instanceof IStaticEnum => false,
			$this instanceof IDynamicEnum => true,
			default => throw new OvommandEnumPoolException("Unknown enum type!", OvommandEnumPoolException::ENUM_UNKNOWN_TYPE)
		};
	}

	/** @return array<string, mixed> */
	public function getRawValues() : array{
		return $this->values;
	}

	/** @return string[] */
	public function getHiddenAliases() : array{
		return $this->hiddenAliases;
	}

	/** @return string[] */
	public function getShowAliases() : array{
		return $this->showAliases;
	}
}
