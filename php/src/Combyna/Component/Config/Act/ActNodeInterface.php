<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Act;

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;

/**
 * Interface ActNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ActNodeInterface extends StructuredNodeInterface
{
    /**
     * Fetches an identifier for this node, usually its type along with its name or index if applicable
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Fetches the type of node, eg. `fixed-static-bag-model`
     *
     * @return string
     */
    public function getType();
}
