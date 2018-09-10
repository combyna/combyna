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
 * Interface ModeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ModeInterface
{
    /**
     * Determines whether this is a development mode
     *
     * @return bool
     */
    public function isDevelopment();
}
