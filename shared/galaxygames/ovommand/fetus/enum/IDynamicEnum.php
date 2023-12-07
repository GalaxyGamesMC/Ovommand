<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\enum;

interface IDynamicEnum extends IOvoEnum{
	public function removeValue(string $key) : void;
	public function removeValuesBySpreading(string ...$keys) : void;
	/** @param string[] $context */
	public function removeValues(array $context) : void;
	public function addValue(string $value, mixed $bindValue = null, string|array $showAliases = [], string|array $hiddenAliases = []) : void;
	public function addValues(array $context, array $showAliases = [], array $hiddenAliases = []) : void;
	public function changeValue(string $key, mixed $value) : void;
}
