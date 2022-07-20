<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Harness;

use Closure;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Class TestCase
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestCase extends PHPUnitTestCase
{
    public function noBind(Closure $closure)
    {
        return function () use ($closure) {
            return call_user_func_array($closure, func_get_args());
        };
    }
}
