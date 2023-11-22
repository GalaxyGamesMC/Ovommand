<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\TargetResult;
use pocketmine\Server;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

class TargetParameter extends BaseParameter{

	public function getValueName() : string{ return "target"; }

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::TARGET;
	}

	public function parse(array $parameters) : BaseResult{
		parent::parse($parameters);
		$parameter = $parameters[0];
		$groups = [];
		if (!preg_match("/^(?:([^\n\w]*@[apres])|([^\d\n@][\w ]*))$/", $parameter, $groups)) {
			return BrokenSyntaxResult::create($parameter, $parameter, $this->getValueName());
		}
		if (isset($groups[2])) {
			$pName = $groups[2];
			if (Server::getInstance()->getPlayerExact($pName) === null) {
				return BrokenSyntaxResult::create($pName, $parameter, $this->getValueName(),);
			}
		}
		return match ($tag = $groups[1]) {
			TargetResult::TARGET_ENTITIES, TargetResult::TARGET_ALL, TargetResult::TARGET_NEAREST_PLAYER, TargetResult::TARGET_RANDOM_PLAYER, TargetResult::TARGET_SELF => TargetResult::create($tag),
			default => TargetResult::create($groups[2])
		};
	}
}
