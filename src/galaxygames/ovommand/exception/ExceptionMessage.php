<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

enum ExceptionMessage : string{
	case ENUM_EMPTY_NAME = "Enum's name cannot be empty";
	case ENUM_ALREADY_EXISTED = "Enum '{enumName}' is already registered";
	case ENUM_ALIAS_REGISTERED = "Alias '{aliasName}' is already used for another key!";
	case ENUM_ALIAS_UNKNOWN_KEY = "Alias '{'aliasName}' is registered to unknown key '{key}'!";
	case ENUM_ALIAS_UNKNOWN_TYPE = "Unknown alias type '{type}' has been registered to key '{key}'";
	case PARAMETER_INVALID_FLAG = "Invalid flag '{flag}', valid flags: (0, 1)";

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
