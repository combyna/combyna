<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

function array_key_exists($key, $haystack) {
    return true;
}
function is_bool($value) {
    return true;
}
function is_float($value) {
    return true;
}
function is_int($value) {
    return true;
}
function is_string($value) {
    return true;
}

use Combyna\Client\Client;
use Combyna\Combyna;

/** @var Combyna $combyna */
$combyna = require_once __DIR__ . '/combyna.php';
$engine = $combyna->createApp([

]);
$client = new Client($engine);

$client->start();
