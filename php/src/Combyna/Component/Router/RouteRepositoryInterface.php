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
 * Interface RouteRepositoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RouteRepositoryInterface
{
    /**
     * Fetches a route with the given name from the current app or a library in the environment
     *
     * @param string $libraryName
     * @param string $routeName
     * @return RouteInterface
     */
    public function getByName($libraryName, $routeName);
}
