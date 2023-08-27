<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

trait CommandEnumTrait{
    protected array $values;

    public function __construct(protected string $name, string|array ...$values){
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
        return array_unique(iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr)), false), SORT_REGULAR);
    }

    final public function collapseArrayKeepStringKeys(array $arr) : array{
        $re = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));
        foreach ($iterator as $key => $value) {
            if (!in_array($value, $re, true)) {
                if (is_string($key)) {
                    $re[$key] = $value;
                } else {
                    $re[] = $value;
                }
            }
        }
        return $re;
    }
    //Todo: this whole mess can be removed if the interface only make it access to value or none!
}
