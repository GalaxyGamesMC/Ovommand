<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\TargetResult;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\Server;

class TargetParameter extends BaseParameter{
	public function hasCompactParameter() : bool { return true; }
	public function getSpanLength() : int{ return 2; }
	public function getValueName() : string{ return "target"; }
	public function getNetworkType() : ParameterTypes{ return ParameterTypes::TARGET; }

	public function parse(array $parameters) : TargetResult|BrokenSyntaxResult{
		$result = parent::parse($parameters);
		if ($result instanceof BrokenSyntaxResult) {
			return $result;
		}
		$parameter = $parameters[0];
		$groups = [];
		if (!preg_match("/^(?:([^\n\w]*@[apres])|([^\d\n@][\w ]*))$/", $parameter, $groups)) {
			return BrokenSyntaxResult::create($parameter, $parameter, $this->getValueName());
		}
		if (isset($groups[2])) {
			$pName = $groups[2];
			if (Server::getInstance()->getPlayerExact($pName) === null) {
				return BrokenSyntaxResult::create($pName, $parameter, $this->getValueName())
					->setCode(BrokenSyntaxResult::CODE_INVALID_INPUTS);
			}
		}
		return match ($tag = $groups[1]) {
			TargetResult::TARGET_ENTITIES, TargetResult::TARGET_ALL, TargetResult::TARGET_NEAREST_PLAYER, TargetResult::TARGET_RANDOM_PLAYER, TargetResult::TARGET_SELF => TargetResult::create($tag),
			default => TargetResult::create($groups[2] ?? "N/A") // TODO: ?? "N/A" is a placeholder
		};
	}
}
