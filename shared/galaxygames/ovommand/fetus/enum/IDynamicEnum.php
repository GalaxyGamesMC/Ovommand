<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\enum;

interface IDynamicEnum extends IEnum{
	public function isProtected() : bool;
	public function asProtected() : ProtectedEnum;
}
