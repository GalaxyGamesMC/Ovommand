<?php
declare(strict_types=1);

namespace galaxygames\ovommand\utils;

use galaxygames\ovommand\BaseCommand;

class Utils{
	public static function parseUsages(BaseCommand $command) : string{
		$usages = ["/" . $command->generateUsageMessage()];
		foreach ($command->getSubCommands() as $subCommand) {
			$usages[] = $subCommand->getUsage();
		}
		$usages = array_unique($usages);
		return implode("\n - /" . $command->getName() . " ", $usages);
	}

	public static function arr(...$arr) : array{
		return $arr;
	}

	public static function collapseNonBindingEnumInputs(array $arr) : array{
		$results = [];

		foreach ($arr as $v) {
			if (!is_string($v)) {
				throw new \RuntimeException("Value is not a string!");
			}
			if (in_array($v, $results, true)) {
				throw new \RuntimeException("Dupe value");
			}
			$results[] = $v;
		}
		return $results;
	}

	public static function collapseEnumInputs(array $arr, bool $isBinding = false) : array{
		return ($isBinding) ? self::collapseBindingEnumInputs($arr) : self::collapseNonBindingEnumInputs($arr);
	}

	public static function collapseBindingEnumInputs(array $arr) : array{
		$results = [];

		foreach ($arr as $k => $v) {
			if (!(is_int($k) || is_string($k))) {
				throw new \RuntimeException("Value is not a string!");
			}
			$results[] = $v;
		}
		return $results;
	}

	public static function validateStringValues(array $arr) : bool{
		foreach ($arr as $v) { //TODO: add exception
			if (!is_string($v)) {
				return false;
			}
		}
		return true;
	}

	/* https://www.php.net/manual/en/language.types.array.php#128741

	https://3v4l.org/0Y6G4 Clas method lol!
	https://3v4l.org/NbjSe, cannot support number keys :c
	 <?php
		function arr(...$array){ return $array;}
		$arr = arr(x: 1, y: 2, z: 3);
		var_dump($arr); // ["x"=>1, "y"=>2, "z"=>3]
	 */

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




	//    /**
	//     * flatten array, and at make the values unique.
	//     *
	//     * @param array $arr
	//     *
	//     * @return array
	//     */
	//
	//    final public static function collapseArrayKeepStringKeys(array $arr) : array{
	//        $re = [];
	//        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));
	//        foreach ($iterator as $key => $value) {
	//            if (!in_array($value, $re, true)) {
	//                if (is_string($key)) {
	//                    $re[$key] = $value;
	//                } else {
	//                    $re[] = $value;
	//                }
	//            }
	//        }
	//        return $re;
	//    }


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
		$obj = new \stdClass();
		foreach ($arr as $key => $value) {
			$rf = $obj->{"" . $key} ?? null;
			if ($rf !== null) {
				throw new \RuntimeException("Duplicated key!");
			}
			$obj->{"" . $key} = $value;
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
