<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

enum ParameterTypes{
	case BLOCK_POSITION;
	case FLOAT;
	case INT;
	case POSITION;
	case STRING;
	case TARGET;
	case TEXT;
	case BLOCK_STATES;
	case COMMAND;
	case COMPARE_OPERATOR;
	case EQUIPMENT_SLOT;
	case FILEPATH;
	case FULL_INTEGER_RANGE;
	case JSON;
	case MESSAGE;
	case OPERATOR;
	case VALUE;
	case WILDCARD_INT;
	case WILDCARD_TARGET;
	case ENUM;
	case SOFT_ENUM;

	public function encode() : int{
		return match ($this) {
			self::BLOCK_POSITION => AvailableCommandsPacket::ARG_TYPE_INT_POSITION,
			self::BLOCK_STATES => AvailableCommandsPacket::ARG_TYPE_BLOCK_STATES,
			self::COMMAND => AvailableCommandsPacket::ARG_TYPE_COMMAND,
			self::COMPARE_OPERATOR => AvailableCommandsPacket::ARG_TYPE_COMPARE_OPERATOR,
			self::EQUIPMENT_SLOT => AvailableCommandsPacket::ARG_TYPE_EQUIPMENT_SLOT,
			self::FILEPATH => AvailableCommandsPacket::ARG_TYPE_FILEPATH,
			self::FLOAT => AvailableCommandsPacket::ARG_TYPE_FLOAT,
			self::FULL_INTEGER_RANGE => AvailableCommandsPacket::ARG_TYPE_FULL_INTEGER_RANGE,
			self::INT => AvailableCommandsPacket::ARG_TYPE_INT,
			self::JSON => AvailableCommandsPacket::ARG_TYPE_JSON,
			self::MESSAGE => AvailableCommandsPacket::ARG_TYPE_MESSAGE,
			self::OPERATOR => AvailableCommandsPacket::ARG_TYPE_OPERATOR,
			self::POSITION => AvailableCommandsPacket::ARG_TYPE_POSITION,
			self::STRING => AvailableCommandsPacket::ARG_TYPE_STRING,
			self::TARGET => AvailableCommandsPacket::ARG_TYPE_TARGET,
			self::TEXT => AvailableCommandsPacket::ARG_TYPE_RAWTEXT,
			self::VALUE => AvailableCommandsPacket::ARG_TYPE_VALUE,
			self::WILDCARD_INT => AvailableCommandsPacket::ARG_TYPE_WILDCARD_INT,
			self::WILDCARD_TARGET => AvailableCommandsPacket::ARG_TYPE_WILDCARD_TARGET,
			self::ENUM => AvailableCommandsPacket::ARG_FLAG_ENUM,
			self::SOFT_ENUM => AvailableCommandsPacket::ARG_FLAG_SOFT_ENUM,
		};
	}
}
