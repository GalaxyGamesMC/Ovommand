<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

abstract class ProtectedEnum implements IEnum{

	public function __construct(protected OvommandEnum $origin){}

	public function getName() : string{
		return $this->origin->getName();
	}

	public function isDefault() : bool{
		return $this->origin->isDefault();
	}

	public function encode() : CommandEnum{
		return $this->origin->encode();
	}

	public function getValue(string $key) : mixed{
		return $this->origin->getValue($key);
	}

	public function isSoft() : bool{
		return $this->origin->isSoft();
	}

	public function getRawValues() : array{
		return $this->origin->getRawValues();
	}

	public function getHiddenAliases() : array{
		return $this->origin->getHiddenAliases();
	}

	public function getShowAliases() : array{
		return $this->origin->getShowAliases();
	}

	abstract public function removeValue(string $key) : void;
	abstract public function removeValuesBySpreading(string ...$keys) : void;
	/** @param string[] $context */
	abstract public function removeValues(array $context) : void;
	abstract public function addValue(string $value, mixed $bindValue = null, string|array $showAliases = [], string|array $hiddenAliases = []) : void;
	abstract public function addValues(array $context, array $showAliases = [], array $hiddenAliases = []) : void;
	abstract public function changeValue(string $key, mixed $value) : void;
}
