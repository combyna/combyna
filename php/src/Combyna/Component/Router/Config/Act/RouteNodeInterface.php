<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Act;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Interface RouteNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RouteNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the unique name of this route
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the name of the page view that should be rendered for this route
     *
     * @return string
     */
    public function getPageViewName();

    /**
     * Fetches the model for the parameter argument static bag this route expects to be extracted from its route segments
     *
     * @param QueryRequirementInterface $queryRequirement
     * @return FixedStaticBagModelNodeInterface
     */
    public function getParameterBagModel(QueryRequirementInterface $queryRequirement);

    /**
     * Fetches the URL pattern for this route
     *
     * @return string
     */
    public function getUrlPattern();
}
