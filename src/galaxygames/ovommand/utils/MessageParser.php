<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

enum MessageParser : string{
	case GENERIC_SYNTAX_MESSAGE_VANILLA = "commands.generic.syntax";
	case GENERIC_SYNTAX_MESSAGE_OVO = "Syntax error: Unexpected \"{broken_syntax}\": at \"{previous}>>{broken_syntax}<<{after}\"";

	case EXCEPTION_ENUM_EMPTY_NAME = "Enum's name cannot be empty!";
	case EXCEPTION_ENUM_NULL_VALUE = "Enum's values cannot be null!";
	case EXCEPTION_ENUM_INVALID_VALUE_NAME_TYPE = "Enum's value-name is not a string!"; //TODO: better msg
	case EXCEPTION_ENUM_ALREADY_EXISTED = "Enum '{enumName}' is already registered!";
	case EXCEPTION_ENUM_ALIAS_REGISTERED = "Alias '{aliasName}' is already used for another key!";
	case EXCEPTION_ENUM_ALIAS_UNKNOWN_KEY = "Alias '{'aliasName}' is registered to unknown key '{key}'!";
	case EXCEPTION_ENUM_ALIAS_UNKNOWN_TYPE = "Unknown alias type '{type}' has been registered to key '{key}'!";
	case EXCEPTION_ENUM_INVALID_DEFAULT = "You cannot set enum '{enumName}' to be default from outside!";
	case EXCEPTION_ENUM_REMOVE_PROTECTED_VALUE = "You cannot remove values from a protected enum '{enumName}'!";
	case EXCEPTION_ENUM_ADD_PROTECTED_VALUE = "You cannot add values from a protected enum '{enumName}'!";
	case EXCEPTION_ENUM_CHANGE_PROTECTED_VALUE = "You cannot change value of key '{key}' from a protected enum '{enumName}'!";
	case EXCEPTION_ENUM_REMOVE_PROTECTED_ALIAS = "HO HO HO";
	case EXCEPTION_ENUM_ADD_PROTECTED_ALIAS = "HAHA";

	case EXCEPTION_PARAMETER_INVALID_FLAG = "Invalid flag '{flag}' was set, valid flags: (0, 1)!";
	case EXCEPTION_PARAMETER_UNKNOWN_ENUM = "Unknown '{enumType}' enum was called '{enumName}'!";
	case EXCEPTION_PARAMETER_NON_OPTIONAL_AFTER_OPTIONAL = "Cannot have non-optional parameters after an optional parameter";
	case EXCEPTION_PARAMETER_AFTER_TEXT_PARAMETER = "Cannot have more parameters after TextParameter";

	case EXCEPTION_SUB_COMMAND_DUPLICATE_ALIAS = "SubCommand with same alias for '{alias}' already exists";
	case EXCEPTION_SUB_COMMAND_DUPLICATE_NAME = "SubCommand with same name for '{subName}' already exists";

	case EXCEPTION_OVOMMANDHOOK_NOT_REGISTERED = "This OvommandHook is not registered with a plugin; please hook it to a plugin before using it for your own goods.";

	case EXCEPTION_BROKEN_SYNTAX_PARSER_COLLIDED_FLAG = "Collided flag, cannot print both vanilla and ovommand message at the same time."; //TODO: BETTER MSG
	case EXCEPTION_BROKEN_SYNTAX_RESULT_INVALID_CODE = "Invalid code '{code}' was set!";

	case EXCEPTION_COORDINATE_RESULT_INVALID_TYPE = "Unknown coordinate's '{name}' value type '{type}' was set!";
	case EXCEPTION_COORDINATE_RESULT_COLLIDED_TYPE = "Once caret, all caret!";
	case EXCEPTION_COORDINATE_RESULT_ENTITY_REQUIRED = "Coords must be returned from the execution by an entity!";

	case CONSTRAINT_INGAME_FAILURE = "This command must be executed from in-game.";
	case CONSTRAINT_CONSOLE_FAILURE = "This command must be executed from server console.";

	/** @param array<string,string> $tags  */
	public function translate(array $tags = []) : string{
		$msg = $this->value;
		foreach ($tags as $tag => $value) {
			$msg = str_replace('{' . $tag . '}', $value, $msg);
		}
		return $msg;
	}
}
