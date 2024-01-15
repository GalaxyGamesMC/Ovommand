<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

class ProtectedEnum implements IEnum{

	public function __construct(protected IDynamicEnum $origin){}

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
		return true;
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
}
