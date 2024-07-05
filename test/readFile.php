<?php
declare(strict_types=1);

function hex2rgb(string $hex) : array{
	sscanf($hex, "#%02x%02x%02x", $r, $g, $b);
	return [$r, $g, $b];
}

$dump = file_get_contents("output1711491551.txt");

$p1 = strpos($dump, "<pre>Name: ") + strlen("<pre>Name: ");
$p2 = strpos($dump, "\n", $p1);
if ($p2 === false) {
	$p2 = strpos($dump, " ", $p1);
}
$name = substr($dump, $p1, $p2 - $p1);
print ("Name: " . $name . "\n");

$dom = new DOMDocument();
$dom->loadHTML($dump);

$span = $dom->getElementsByTagName("span");
$line = -1;
$nline = 0;
foreach ($span as $s) {
	$span_style = $s->getAttribute('style');
	$text = $s->ownerDocument->saveXML($s);
	$nline += substr_count($text, "\n");
	print("Line: " . $nline . "\n");
	if ($line !== -1 && $nline > $line) {
		$line = $nline;
		print("\nDOWN\n");
	}
	if (!str_contains($span_style, "color")) {
		continue;
	}
	$p1 = strpos($span_style, "color: #") + strlen("color: ");
	$p2 = strpos($span_style, ";", $p1);
	if ($p2 === false) {
		$hex1 = substr($span_style, $p1);
	} else {
		$hex1 = substr($span_style, $p1, $p2 - $p1);
		$p2 = strpos($span_style, "#", $p1 + 1);
		$hex2 = substr($span_style, $p2);
	}
//	print("Color: " . $hex1 . "\n");
//	print("Background: " . (empty($hex2) ? "N\A" : $hex2) . "\n");
//	print("Value: " . $s->nodeValue . "\n");
}