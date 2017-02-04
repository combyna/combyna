<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Harness;

use Closure;
use Concise\Core\TestCase as ConciseTestCase;

/**
 * Class TestCase
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestCase extends ConciseTestCase
{
    public function noBind(Closure $closure)
    {
        return function () use ($closure) {
            return call_user_func_array($closure, func_get_args());
        };
    }
}
