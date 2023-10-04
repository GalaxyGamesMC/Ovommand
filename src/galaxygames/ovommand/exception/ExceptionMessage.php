<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

enum ExceptionMessage : string{
	case MSG_ENUM_EMPTY_NAME = "Enum's name cannot be empty";
	case MSG_ENUM_FAILED_OVERLAY = "Enum '{enumName}' is already registered";
	case MSG_ENUM_ALIAS_REGISTERED = "Alias '{aliasName}' is already used for another key!";
	case MSG_ENUM_ALIAS_UNKNOWN_KEY = "Alias '{'aliasName}' is registered to unknown key '{key}'!";
	case MSG_ENUM_ALIAS_UNKNOWN_TYPE = "Alias '{'aliasName}' type is expected to be either string or string list, type '{type}' given!";
	case MSG_DUPLICATED_NAME_IN_OTHER_TYPE = "'{enumName}' is already existed as an {enumType}";
	case MSG_PARAMETER_INVALID_VALUE = "Invalid value '{value}' for parameter #{position}";
	case MSG_PARAMETER_NO_VALUE = "No arguments are required for this command";
	case MSG_PARAMETER_INVALID_FLAG = "Invalid flag '{flag}', valid flags: (0, 1)";
	case MSG_PARAMETER_NEGATIVE_ORDER = "You cannot register parameter at negative positions #{position}";
	case MSG_PARAMETER_DETACHED_ORDER = "There were no parameters before #{position}";
	case MSG_PARAMETER_DESTRUCTED_ORDER = "You cannot register a required Parameter after an optional parameter";

	/**
	 * @param array<string,string> $tags
	 */
	public function getErrorMessage(array $tags) : string{
		$msg = $this->value;
		foreach ($tags as $tag => $value) {
			$msg = str_replace('{' . $tag . '}', $value, $msg);
		}
		return $msg;
	}

	public function getRawErrorMessage() : string{
		return $this->value;
	}
}
