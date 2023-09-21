<?php
declare(strict_types=1);
require_once "D:\phpstorm2\Ovommand\src\galaxygames\ovommand\parameter\parse\Coordinates.php";
require_once "D:\phpstorm2\Ovommand\src\galaxygames\syntax\SyntaxConst.php";

use galaxygames\ovommand\parameter\parse\Coordinates;
use galaxygames\ovommand\syntax\SyntaxConst;

SyntaxConst::setSyntax(SyntaxConst::SYNTAX_PRINT_OVO_VANILLA);

function redmsg(string $msg) : string{
    return "\033[01;31m " . $msg . " \033[0m";
}

//function dumpUnexpected(string $raw, string $unexpected, string $helper = ""){
//    if ($helper !== "") {
//        $helper = ". Suggest: \"" . $helper . "\"";
//    }
//    return "Syntax error: Unexpected \"$unexpected\": at \"" . str_replace($unexpected, ">>$unexpected<<", $raw) . "\"" . $helper;
//}

function canParseArgs(array $args, bool $helper = false) : ?Coordinates{
    if (count($args) > 3) {
        throw new \InvalidArgumentException("Too many args");
    }
    $unmatch = "";
    $genType = null;
    $types = [];
    $values = [];
    $helps = "";
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
            "~" => Coordinates::TYPE_RELATIVE,
            "^" => Coordinates::TYPE_LOCAL,
            default => Coordinates::TYPE_DEFAULT
        };
        if ($genType === null) {
            $genType = $type;
        }
        if ($type === Coordinates::TYPE_LOCAL && $genType !== Coordinates::TYPE_LOCAL) {
            $unmatch = $arg;
            $helps ="^->?";
            break;
        }
        if ($genType === Coordinates::TYPE_LOCAL && $type !== Coordinates::TYPE_LOCAL) {
            $unmatch = $arg;
            $helps = $u . "->^";
            break;
        }
        $value = ltrim($arg, $u);
        $nValue = str_contains($value, ".") ? (double) $value : (int) $value;
        $types[$i] = $type;
        $values[$i] = $nValue;
    }
    if ($unmatch !== "") {
        $syntax = implode(" ", $args);
        echo redmsg(SyntaxConst::parseSyntax($args[0] ?? "", $unmatch, $args[2] ?? "", $helper ? $helps : "")) . "\n";
        return null;
    }
    return Coordinates::fromData(...$values, ...$types);
}

$in = ["^+1.5213111123456789", "~14141.12749813","^1414.2421"];
// Result: Syntax error: Unexpected "~14141.12749813": at "123456789>>~14141.12749813<<1414.2421"
// Vanilla: Syntax error: Unexpected "~14141.12749813": at "123456789>>~14141.12749813<<1414.2421"
$coord = canParseArgs($in, true);
echo($coord) . "\n";

//TODO: debug msg length