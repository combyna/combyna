<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Config\Act;

use Combyna\Component\Config\Act\DynamicActNodeInterface;

/**
 * Interface DynamicActNodeAdopterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DynamicActNodeAdopterInterface
{
    /**
     * Applies the validation for the provided dynamically-created ACT node
     *
     * @param DynamicActNodeInterface $actNode
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $actNode);
}
