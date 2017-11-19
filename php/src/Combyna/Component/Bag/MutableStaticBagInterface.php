<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\StaticInterface;

/**
 * Interface MutableStaticBagInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface MutableStaticBagInterface extends StaticBagInterface
{
    /**
     * Assigns a new value for a static in this bag
     *
     * @param string $name
     * @param StaticInterface $value
     */
    public function setStatic($name, StaticInterface $value);
}
