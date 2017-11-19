<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router;

/**
 * Interface RouteCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RouteCollectionInterface
{
    /**
     * Fetches a route with the given name from the collection
     *
     * @param string $name
     * @return RouteInterface
     */
    public function getByName($name);
}
