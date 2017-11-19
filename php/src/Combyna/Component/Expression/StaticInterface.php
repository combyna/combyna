<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

/**
 * Interface StaticInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StaticInterface extends ExpressionInterface
{
    /**
     * Fetches the native value of this static
     *
     * @return mixed
     */
    public function toNative();
}
