#!/usr/bin/env php
<?php

require __DIR__ . '/../src/Keys.php';

use Tapbuy\DataScrubber\Keys;

$url = $argv[1];

if (empty($url)) {
    echo "Usage: php bin/updateKeys.php <url>\n";
    exit(1);
}

try {
    $fetcher = new Keys($url);
    $fetcher->fetchKeys();
    echo "Keys updated\n";
} catch (Exception $e) {
    echo "Error : " . $e->getMessage() . "\n";
    exit(1);
}
