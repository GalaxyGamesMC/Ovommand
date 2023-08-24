<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

/**
 * @deprecated Old API
 */
abstract class CustomEnum{

	protected array $values;

	public function __construct(protected string $name, string ...$values){
		$this->values = $this->collapseArray($values);
	}

	final public function getName() : string{
		return $this->name;
	}

	/** @internal */
	abstract public function encode() : CommandEnum;

    /**
     * flatten array, and at make the values unique.
     *
     * @param array $arr
     *
     * @return array
     */
    final public function collapseArray(array $arr) : array{
        return array_unique(iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr)), false));
    }
}