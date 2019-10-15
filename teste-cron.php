<?php

$data = "exec em: ". date("Y-m-d H:i:s") . PHP_EOL;
$filename = dirname(__FILE__) . "/output.txt";

file_put_contents($filename, $data, FILE_APPEND);
