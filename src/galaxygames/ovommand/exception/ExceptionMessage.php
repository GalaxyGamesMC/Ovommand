<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

enum ExceptionMessage : string{
	case ENUM_EMPTY_NAME = "Enum's name cannot be empty!";
	case ENUM_ALREADY_EXISTED = "Enum '{enumName}' is already registered!";
	case ENUM_ALIAS_REGISTERED = "Alias '{aliasName}' is already used for another key!";
	case ENUM_ALIAS_UNKNOWN_KEY = "Alias '{'aliasName}' is registered to unknown key '{key}'!";
	case ENUM_ALIAS_UNKNOWN_TYPE = "Unknown alias type '{type}' has been registered to key '{key}'!";
	case ENUM_INVALID_DEFAULT = "You cannot set enum '{enumName}' to be default from outside!";

	case PARAMETER_INVALID_FLAG = "Invalid flag '{flag}' was set, valid flags: (0, 1)!";
	case PARAMETER_UNKNOWN_ENUM = "Unknown '{enumType}' enum was called '{enumName}'!";
	case PARAMETER_NON_OPTIONAL_AFTER_OPTIONAL = "Cannot have non-optional parameters after an optional parameter";
	case PARAMETER_AFTER_TEXT_PARAMETER = "Cannot have more parameters after TextParameter";

	case SUB_COMMAND_DUPLICATE_ALIAS = "SubCommand with same alias for '{alias}' already exists";
	case SUB_COMMAND_DUPLICATE_NAME = "SubCommand with same name for '{subName}' already exists";

	/** @param array<string,string> $tags  */
	public function translate(array $tags = []) : string{
		$msg = $this->value;
		foreach ($tags as $tag => $value) {
			$msg = str_replace('{' . $tag . '}', $value, $msg);
		}
		return $msg;
	}

	public function getText() : string{
		return $this->value;
	}
}
