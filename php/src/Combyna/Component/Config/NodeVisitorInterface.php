<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config;

/**
 * Interface NodeVisitorInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface NodeVisitorInterface
{
    /**
     * Visits a node and returns its new value
     *
     * @param mixed $node
     * @return mixed|array
     */
    public function visit($node);
}
