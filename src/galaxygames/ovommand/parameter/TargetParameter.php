<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;
use galaxygames\ovommand\parameter\result\TargetResult;
use galaxygames\ovommand\utils\SyntaxConst;
use pocketmine\Server;
use shared\galaxygames\ovommand\fetus\result\BaseResult;

class TargetParameter extends BaseParameter{

	public function getValueName() : string{ return "target"; }

	public function getNetworkType() : ParameterTypes{
		return ParameterTypes::TARGET;
	}

	// rgx-tag https://rubular.com/r/aAcqUeP3S9wPKx

	// latest https://rubular.com/r/FJiwYBmY0IoQ0J

	//TODO: edu version have aprescv, not just apres

	public function parse(array $parameters) : BaseResult{
		parent::parse($parameters);
		$parameter = $parameters[0];
		$groups = [];
		if (!preg_match("/^(?:([^\n\w]*@[apres])|([^\d\n@][\w ]*))$/", $parameter, $groups)) {
			$syntax = SyntaxConst::getSyntaxBetweenBrokenPart(implode(" ", $parameters), $parameter);
			return BrokenSyntaxResult::create(SyntaxConst::parseSyntax($syntax[0], $parameter, $syntax[1]) ?? "");
		}
		if (isset($groups[2])) {
			$pName = $groups[2];
			if (Server::getInstance()->getPlayerExact($pName) === null) {
				return BrokenSyntaxResult::create($pName);
			}
		}
		return match ($tag = $groups[1]) {
			TargetResult::TARGET_ENTITIES, TargetResult::TARGET_ALL, TargetResult::TARGET_NEAREST_PLAYER, TargetResult::TARGET_RANDOM_PLAYER, TargetResult::TARGET_SELF => TargetResult::create($tag),
			default => TargetResult::create($groups[2])
		};
	}

	//	public function betaParse(array $parameters) : BaseResult{
	//		parent::parse($parameters);
	//		$parameter = $parameters[0];
	//		$groups = [];
	//		if (!preg_match("/^(?:([^\n]*@[apres])(\S*)|([\w ][^\n]*))$/", $parameter, $groups)) {  //rgx2
	//			$syntax = SyntaxConst::getSyntaxBetweenBrokenPart(implode(" ", $parameters), $parameter);
	//			return BrokenSyntaxResult::create(SyntaxConst::parseSyntax($syntax[0], $parameter, $syntax[1]) ?? "");
	//		}
	//		return match ($tag = $groups[1]) {
	//			TargetResult::TARGET_ENTITIES, TargetResult::TARGET_ALL, TargetResult::TARGET_NEAREST_PLAYER, TargetResult::TARGET_RANDOM_PLAYER, TargetResult::TARGET_SELF => TargetResult::create($tag),
	//			default => TargetResult::create($groups[2])
	//		};
	//	}
}
