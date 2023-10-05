<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\enum;

interface IDefaultEnum extends IEnum{
	public function isVanilla() : bool;
	public function isSoft() : bool;
}
