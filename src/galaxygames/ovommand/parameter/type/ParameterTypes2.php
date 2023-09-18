<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\type;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\utils\EnumTrait;

/**
 * @method static ParameterTypes2 BLOCK_POSITION()
 * @method static ParameterTypes2 FLOAT()
 * @method static ParameterTypes2 INT()
 * @method static ParameterTypes2 POSITION()
 * @method static ParameterTypes2 STRING()
 * @method static ParameterTypes2 TARGET()
 * @method static ParameterTypes2 TEXT()
 * @method static ParameterTypes2 BLOCK_STATES()
 * @method static ParameterTypes2 COMMAND()
 * @method static ParameterTypes2 COMPARE_OPERATOR()
 * @method static ParameterTypes2 EQUIPMENT_SLOT()
 * @method static ParameterTypes2 FILEPATH()
 * @method static ParameterTypes2 FULL_INTEGER_RANGE()
 * @method static ParameterTypes2 JSON()
 * @method static ParameterTypes2 MESSAGE()
 * @method static ParameterTypes2 OPERATOR()
 * @method static ParameterTypes2 VALUE()
 * @method static ParameterTypes2 YELLOW()
 * @method static ParameterTypes2 WILDCARD_INT()
 * @method static ParameterTypes2 WILDCARD_TARGET()
 */
final class ParameterTypes2{
	use EnumTrait {
		EnumTrait::__construct as Enum___construct;
	}

	protected static function setup() : void{
		self::registerAll(new ParameterTypes2("block_position", "", AvailableCommandsPacket::ARG_TYPE_INT_POSITION), new ParameterTypes2("block_states", "", AvailableCommandsPacket::ARG_TYPE_BLOCK_STATES), new ParameterTypes2("command ", "", AvailableCommandsPacket::ARG_TYPE_COMMAND), new ParameterTypes2("compare_operator ", "", AvailableCommandsPacket::ARG_TYPE_COMPARE_OPERATOR), new ParameterTypes2("equipment_slot ", "", AvailableCommandsPacket::ARG_TYPE_EQUIPMENT_SLOT), new ParameterTypes2("filepath ", "", AvailableCommandsPacket::ARG_TYPE_FILEPATH), new ParameterTypes2("float ", "", AvailableCommandsPacket::ARG_TYPE_FLOAT), new ParameterTypes2("full_integer_range ", "", AvailableCommandsPacket::ARG_TYPE_FULL_INTEGER_RANGE), new ParameterTypes2("int ", "", AvailableCommandsPacket::ARG_TYPE_INT), new ParameterTypes2("json ", "", AvailableCommandsPacket::ARG_TYPE_JSON), new ParameterTypes2("message ", "", AvailableCommandsPacket::ARG_TYPE_MESSAGE), new ParameterTypes2("operator ", "", AvailableCommandsPacket::ARG_TYPE_OPERATOR), new ParameterTypes2("position ", "", AvailableCommandsPacket::ARG_TYPE_POSITION), new ParameterTypes2("string ", "", AvailableCommandsPacket::ARG_TYPE_STRING), new ParameterTypes2("target", "", AvailableCommandsPacket::ARG_TYPE_TARGET), new ParameterTypes2("text", "", AvailableCommandsPacket::ARG_TYPE_RAWTEXT), new ParameterTypes2("value ", "", AvailableCommandsPacket::ARG_TYPE_VALUE), new ParameterTypes2("wildcard_int ", "", AvailableCommandsPacket::ARG_TYPE_WILDCARD_INT), new ParameterTypes2("wildcard_target ", "", AvailableCommandsPacket::ARG_TYPE_WILDCARD_TARGET),);
	}

	private function __construct(string $enumName, private string $regex, private int $type){
		$this->Enum___construct($enumName);
	}

	public function getRegex() : string{
		return $this->regex;
	}

	public function getType() : int{
		return $this->type;
	}
}
