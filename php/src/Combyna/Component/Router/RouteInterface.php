<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router;

use Combyna\Component\Bag\StaticBagInterface;

/**
 * Interface RouteInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RouteInterface
{
    /**
     * Checks that the given static bag matches the parameters and their types defined for this route
     *
     * @param StaticBagInterface $argumentBag
     */
    public function assertValidArgumentBag(StaticBagInterface $argumentBag);

    /**
     * Fetches the unique name of this route within the app
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the name of the page view to display for this route
     *
     * @return string
     */
    public function getPageViewName();
}
