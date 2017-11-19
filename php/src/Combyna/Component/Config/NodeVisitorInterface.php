<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config;

use Combyna\Component\Common\DelegatorInterface;

/**
 * Interface NodeVisitorInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface NodeVisitorInterface extends DelegatorInterface
{
    /**
     * Visits a node and returns its new value
     *
     * @param mixed $node
     * @return mixed|array
     */
    public function visit($node);
}
