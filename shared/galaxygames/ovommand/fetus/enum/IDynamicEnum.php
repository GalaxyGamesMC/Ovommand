<?php
declare(strict_types=1);

namespace shared\galaxygames\ovommand\fetus\enum;

interface IDynamicEnum{

	public function removeValue(string $key) : void;

	public function removeValuesBySpreading(string ...$keys) : void;

	/**
	 * @param string[] $context
	 */
	public function removeValues(array $context) : void;

	public function addValue(string $value, mixed $bindValue = null) : void;

	/**
	 * @param array<string,mixed> $context
	 */
	public function addValues(array $context) : void;

	public function changeValue(string $key, mixed $value) : void;
}
