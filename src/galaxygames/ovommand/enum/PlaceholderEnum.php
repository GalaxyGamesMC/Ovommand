<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

class PlaceholderEnum extends BaseEnum{
	protected bool $isSoft = false;

	public static function create(string $name, bool $isSoft = false) : self{
		return (new self($name))->setType($isSoft);
	}

	public function setType(bool $isSoft = false) : self{
		$this->isSoft = $isSoft;
		return $this;
	}

	public function isSoft() : bool{
		return $this->isSoft;
	}

	public function encode() : CommandEnum{
		return new CommandEnum($this->name, []);
	}
}
