<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework\Mode;

/**
 * Class ProductionMode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ProductionMode implements ModeInterface
{
    /**
     * {@inheritdoc}
     */
    public function isDevelopment()
    {
        return false;
    }
}
