<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

enum ExceptionMessage : string{
	case ENUM_EMPTY_NAME = "Enum's name cannot be empty";
	case ENUM_ALREADY_EXISTED = "Enum '{enumName}' is already registered";
	case ENUM_ALIAS_REGISTERED = "Alias '{aliasName}' is already used for another key!";
	case ENUM_ALIAS_UNKNOWN_KEY = "Alias '{'aliasName}' is registered to unknown key '{key}'!";
	case ENUM_ALIAS_UNKNOWN_TYPE = "Unknown alias type '{type}' has been registered to key '{key}'";
	case PARAMETER_INVALID_VALUE = "Invalid value '{value}' for parameter #{position}";
	case PARAMETER_NO_VALUE = "No arguments are required for this command";
	case PARAMETER_INVALID_FLAG = "Invalid flag '{flag}', valid flags: (0, 1)";
	case PARAMETER_NEGATIVE_ORDER = "You cannot register parameter at negative positions #{position}";
	case PARAMETER_DETACHED_ORDER = "There were no parameters before #{position}";
	case PARAMETER_DESTRUCTED_ORDER = "You cannot register a required Parameter after an optional parameter";

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
