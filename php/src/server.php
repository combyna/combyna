<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

use Combyna\Server\Server;

$combyna = require_once __DIR__ . '/combyna.php';
$engine = $combyna->createEngine();
$server = new Server($engine);

$server->start();
