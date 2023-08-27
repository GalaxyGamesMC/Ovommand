<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

use galaxygames\ovommand\BaseCommand;
use stdClass;
use function implode;

class Utils{
    public static function parseUsages(BaseCommand $command) : string{
        $usages = ["/" . $command->generateUsageMessage()];
        foreach($command->getSubCommands() as $subCommand) {
            $usages[] = $subCommand->getUsageMessage();
        }
        $usages = array_unique($usages);
        return implode("\n - /" . $command->getName() . " ", $usages);
    }

    /**
     * flatten array, and at make the values unique
     *
     * @param array $arr
     *
     * @return array
     */
    public static function collapseArray(array $arr) : array{
        return array_unique(iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr)), false));
    }

    public static function validateNonBindingStringList(array $arr) : array{
        $re = [];
        foreach ($arr as $value) {
           if (is_string($value)) {
               $re[] = $value;
           } else {
               throw new \InvalidArgumentException("adakjhakda");
           }
        }
        return $re;
    }

    public static function validateBindingStringMixedArray(array $arr) : array{
        $re = [];
        foreach ($arr as $key => $value) {
            if (!is_string($key)) {
                throw new \RuntimeException("KEY MUST BE STRING");
            }
            $re[$key] = $value;
        }
        return $re;
    }

    public static function dumpForceStringList(array $arr) : array{
        // cannot convert class / object to string!
        return @explode("\n", implode("\n", $arr));
    }

    public static function dumpForceStringArray(array $arr) : array{
            $obj = new stdClass();
            foreach ($arr as $key => $value) {
                $rf = $obj->{$key} ?? null;
                if ($rf !== null) {
                    throw new \RuntimeException("Duplicated key!");
                }
                $obj->{$key} = $value;
            }
            return (array) $obj;
    }

    public static function dumpForceStringArray2(array $arr) : array{
        $re = [];
        foreach ($arr as $key => $value) {
            if (isset($re[$key])) {
                throw new \RuntimeException("Duplicated key!");
            }
            $re[(string) $key] = $value;
        }
        return $re;
    }

    public static function collapseArrayKeepStringKeys(array $arr) : array{
        $re = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));
        foreach ($iterator as $key => $value) {
            if (!in_array($value, $re, true)) {
                if (is_string($key)) {
                    $re[$key] = $value;
                } else {
                    $re[] = $value;
                }
            }
        }
        return $re;
    }
}
