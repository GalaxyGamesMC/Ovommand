<?php
declare(strict_types=1);

namespace galaxygames\ovommand\exception;

final class EnumException extends OvommandException{
    public const ENUM_EMPTY_NAME_ERROR = 0;
    public const ENUM_FAILED_OVERLAY_ERROR = 1;
    public const ENUM_DUPLICATED_NAME_IN_OTHER_TYPE_ERROR = 2; // lol
}