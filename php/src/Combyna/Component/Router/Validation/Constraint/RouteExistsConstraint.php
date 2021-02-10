<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Validation\Constraint;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class RouteExistsConstraint
 *
 * Ensures that the specified expression evaluates to a valid fully-qualified route name,
 * either as a statically provided string value or as an Exotic type that provides a route_name.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteExistsConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $routeName;

    /**
     * @param string $libraryName
     * @param string $routeName
     */
    public function __construct($libraryName, $routeName)
    {
        $this->libraryName = $libraryName;
        $this->routeName = $routeName;
    }

    /**
     * Fetches the name of the library that should define the route
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the name of the route
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
