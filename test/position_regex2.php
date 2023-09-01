<?php
declare(strict_types=1);
function redmsg(string $msg) {
    return "\033[01;31m " . $msg . " \033[0m";
}

function dumpUnexpected(string $raw, string $unexpected){
    return "Syntax error: Unexpected \"$unexpected\": at \"" . str_replace($unexpected, ">>$unexpected<<", $raw) . "\"";
}

enum Type{
    case DEFAULT;
    case RELATIVE;
    case LOCALE;
}

function canParseArgs(array $args) : bool{
    $unmatch = "";
    $genType = null;
    $types = [];
    foreach ($args as $i => $arg) {
        if (str_contains($arg, " ")) {
            $unmatch = $arg;
            break;
        }
        if (!preg_match("/^([~^]?[+-]?\d+(?:\.\d+)?)$/", $arg)) {
            $unmatch = $arg;
            break;
        }
        $type = match($u = substr($arg, 0, 1)) {
            "~" => Type::RELATIVE,
            "^" => TYPE::LOCALE,
            default => Type::DEFAULT
        };
        if ($genType === null) {
            $genType = $type;
        }
        if ($type === Type::LOCALE && $genType !== Type::LOCALE) {
            $unmatch = $arg;
            break;
        }
        if ($genType === Type::LOCALE && $type !== Type::LOCALE) {
            $unmatch = $arg;
            break;
        }
        $types[$i] = $type;
    }
    if ($unmatch !== "") {
        $syntax = implode(" ", $args);
        echo redmsg(dumpUnexpected($syntax, $unmatch)) . "\n";
        return false;
    }
    var_dump($types);
    return true;
}
$in = ["^+1.5213", "^14141.12749813","~1414.2421"]; // ~+1.5213 ^14141. 12749813 ^1414.2421
canParseArgs($in);

//TODO: debug msg length