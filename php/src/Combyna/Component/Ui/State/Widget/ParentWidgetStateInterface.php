<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\Widget;

/**
 * Interface ParentWidgetStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ParentWidgetStateInterface
{
    /**
     * Fetches the specified child widget state of this one
     *
     * @param string $name
     * @return WidgetStateInterface
     */
    public function getChildState($name);
}
