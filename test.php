<?php

require "vendor/autoload.php";

if ($argc !== 3) {
    echo "Usage: $argv[0] [parallel-worker:int] [current-worker:int]\n";
    exit(1);
}

$parallelWorkers = (int)$argv[1];
$currentWorker = (int)$argv[2];
if ($parallelWorkers <= 0) {
    echo "parallel-worker should be a positive integer\n";
    exit(2);
}
if ($currentWorker < 0 || $currentWorker >= $parallelWorkers) {
    echo "current-worker should be a integer between 0 and parallel-worker minus 1\n";
    exit(3);
}

$tests = shell_exec("./vendor/bin/simple-phpunit --list-tests");
if (!is_string($tests)) {
    exit("no test or error");
}

$tests = array_filter(explode(PHP_EOL, $tests));
$tests = array_map(fn($test) => substr($test, 3), $tests);

$workersTests = [];
foreach ($tests as $key => $test) {
    $workersTests[$key % $parallelWorkers][] = $test;
}

echo "Tests count: " . count($tests) . PHP_EOL;
echo "Worker test count: " . count($workersTests[$currentWorker]) . PHP_EOL;
