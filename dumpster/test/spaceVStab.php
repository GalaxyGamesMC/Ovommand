<?php
declare(strict_types=1);

$e = 10000000;

$tab = str_repeat("\t", $e);
$smartTab = str_repeat(" ", $e*4);

mkdir("text/");
file_put_contents("text/tab.txt", $tab); //Tab win, by a lot c: //9,53 MB
file_put_contents("text/smartTab.txt", $smartTab); //38,1 MB