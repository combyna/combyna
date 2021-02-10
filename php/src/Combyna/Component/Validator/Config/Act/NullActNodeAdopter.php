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
 * Class NullActNodeAdopter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NullActNodeAdopter implements DynamicActNodeAdopterInterface
{
    /**
     * {@inheritdoc}
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $actNode)
    {
        // Nothing to do, no adoption needs to be performed
    }
}
