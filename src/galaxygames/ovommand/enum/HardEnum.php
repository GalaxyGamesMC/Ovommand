<?php
declare(strict_types=1);

namespace galaxygames\ovommand\enum;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

class HardEnum{
    protected array $values;
    protected array $aliases;

    public function __construct(protected string $name, ){
        $this->values = $this->collapseBind($values);
        $this->aliases = $this->collapseAliases($aliases);
    }

    private function collapseBind(array $arr) : array{
        $isBound = array_is_list($arr);
        $res = [];
        if ($isBound) {
            foreach ($arr as $value) {
                $res[$value] = $value;
            }
        } else {
            foreach ($arr as $item => $value) {
                $res[$item] = $value;
            }
        }
        return $res;
    }

    private function collapseAliases(array $aliases) : array{
        $res = [];
        foreach ($aliases as $value => $alias) {
            if (is_string($alias)) {
                throw new \RuntimeException("Invalid type!");
            }
            if (isset($this->values[$value])) {
                $res[$value] = $aliases;
            } else {
                throw new \RuntimeException("Unknown aliases for value!"); //TODO: better msg
            }
        }
        return $res;
    }

    final public function getName() : string{
        return $this->name;
    }

    public function parseValue(string $key) : bool{

    }

	public function encode() : CommandEnum{
		return new CommandEnum($this->name, $this->values);
	}
}
