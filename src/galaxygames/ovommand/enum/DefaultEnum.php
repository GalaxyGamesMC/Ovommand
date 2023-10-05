<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use galaxygames\ovommand\enum\BaseEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use shared\galaxygames\ovommand\fetus\enum\IDefaultEnum;

class DefaultEnum extends BaseEnum implements IDefaultEnum{
	public function __construct(protected string $name, protected bool $isSoft = false, array $values = [], array $showAliases = [], array $hiddenAliases = [], protected bool $isVanilla = false, ){
		parent::__construct($this->name, $values, $showAliases, $hiddenAliases);
	}

	public function encode() : CommandEnum{
		return new CommandEnum($this->name, [...array_keys($this->values), ...array_keys($this->showAliases)], $this->isSoft);
	}

	public function isVanilla() : bool{
		return $this->isVanilla;
	}

	public function isSoft() : bool{
		return $this->isSoft;
	}

	public function setVanilla(bool $isVanilla = true) : void{
		$this->isVanilla = $isVanilla;
	}
}
