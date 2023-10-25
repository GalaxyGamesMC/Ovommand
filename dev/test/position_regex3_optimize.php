<?php
declare(strict_types=1);
require_once "D:\phpstorm2\Ovommand\src\galaxygames\ovommand\parameter\parse\Coordinates.php";
require_once "D:\phpstorm2\Ovommand\src\galaxygames\syntax\SyntaxConst.php";

use galaxygames\ovommand\parameter\result\CoordinateResult;
use galaxygames\ovommand\utils\SyntaxConst;

function redmsg(string $msg) : string{
    return "\033[01;31m " . $msg . " \033[0m";
}

function canParseArgs(array $args, bool $helper = false) : ?CoordinateResult{
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
            "~" => CoordinateResult::TYPE_RELATIVE,
            "^" => CoordinateResult::TYPE_LOCAL,
            default => CoordinateResult::TYPE_DEFAULT
        };
        if ($genType === null) {
            $genType = $type;
        }
        if ($type === CoordinateResult::TYPE_LOCAL && $genType !== CoordinateResult::TYPE_LOCAL) {
            $unmatch = $arg;
            $helps ="^->?";
            break;
        }
        if ($genType === CoordinateResult::TYPE_LOCAL && $type !== CoordinateResult::TYPE_LOCAL) {
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
//        // https://3v4l.org/HjgV1
//
//        $syntax = implode(" ", $args);
//        $unmatch_pos = strpos($syntax, $unmatch);
////        if ($unmatch_pos === false) {
////            throw new \RuntimeException("wut??");
////        }
//        $previous = substr($syntax, 0, $unmatch_pos);
//        $after = substr($syntax, $unmatch_pos + strlen($unmatch));
        $syntax = SyntaxConst::getSyntaxBetweenBrokenPart(implode(" ", $args), $unmatch);
        SyntaxConst::setSyntax(SyntaxConst::SYNTAX_PRINT_OVO_VANILLA);
        echo redmsg(SyntaxConst::parseSyntax($syntax[0], $unmatch, $syntax[1], $helper ? $helps : "")) . "\n";
        SyntaxConst::setSyntax(SyntaxConst::SYNTAX_PRINT_OVO_FULL);
        echo redmsg(SyntaxConst::parseSyntax($syntax[0], $unmatch, $syntax[1], $helper ? $helps : "")) . "\n";
        return null;
    }
    return CoordinateResult::fromData(...$values, ...$types);
}

$in = ["^+1.5213111123456789", "~14141.12749813","^1414.2421"];
$coord = canParseArgs($in, false);

// Helper: true
// Syntax error: Unexpected "~14141.12749813": at "23456789 >>~14141.12749813<<1414.2421". Suggest: "~->^"
// Syntax error: Unexpected "~14141.12749813": at "^+1.5213111123456789 >>~14141.12749813<< ^1414.2421". Suggest: "~->^"

// Helper: false
// Syntax error: Unexpected "~14141.12749813": at "23456789 >>~14141.12749813<<1414.2421"
// Syntax error: Unexpected "~14141.12749813": at "^+1.5213111123456789 >>~14141.12749813<< ^1414.2421"

echo($coord) . "\n";

//TODO: debug msg length