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

use Combyna\Component\Bag\FixedStaticBagModelInterface;
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
     * Generates a URL for this route given a set of arguments for its parameters
     *
     * @param StaticBagInterface $argumentBag
     * @return string
     */
    public function generateUrl(StaticBagInterface $argumentBag);

    /**
     * Fetches the name of the library this route belongs to.
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the name of the page view to display for this route
     *
     * @return string
     */
    public function getPageViewName();

    /**
     * Fetches the model for the parameter argument static bag this route expects to be extracted from its route segments
     *
     * @return FixedStaticBagModelInterface
     */
    public function getParameterBagModel();

    /**
     * Fetches the unique name of this route within its library.
     *
     * @return string
     */
    public function getRouteName();

    /**
     * Fetches the URL pattern for this route
     *
     * @return string
     */
    public function getUrlPattern();
}
