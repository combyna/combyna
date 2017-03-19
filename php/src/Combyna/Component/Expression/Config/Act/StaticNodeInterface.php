<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

/**
 * Interface StaticNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StaticNodeInterface extends ExpressionNodeInterface
{
    /**
     * Fetches the native value of this static
     *
     * @return mixed
     */
    public function toNative();
}
