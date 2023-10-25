<?php
$input= "Arie19062022";

$list = array(
	"Arie1936",
	"Arie1231",
	"xxArie11",
	"Arie1906",
	"Meow meow",
	"Lol"
);

$percent_old = 0;

if (empty($list)) {
	return;
}
$finalKey = 0;

foreach ($list as $key => $value ) {
	similar_text($input, $value, $percent);

	if ($percent > $percent_old) {
		$percent_old = $percent; # assign $percent to $percent_old
		$finalKey = $key; # assign $key to $final_result
	}
}

$found = $list[$finalKey];
if ($found === $input) {
	print("Found '$input' \n");
} else {
	print("Couldn't find '$input', did you mean '$found'?\n");
}