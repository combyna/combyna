<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Plugin\Core;

use Combyna\Component\Plugin\AbstractPlugin;

/**
 * Class CorePlugin
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CorePlugin extends AbstractPlugin
{
    /**
     * The Core library defines the special widget types "group", "repeater", "text" etc.
     */
    const CORE_LIBRARY = 'core';

    /**
     * The List library defines functions for manipulating Lists in expressions
     */
    const LIST_LIBRARY = 'list';
}
