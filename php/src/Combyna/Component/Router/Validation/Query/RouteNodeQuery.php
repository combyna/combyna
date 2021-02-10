<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\ActNodeQueryInterface;

/**
 * Class RouteNodeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteNodeQuery implements ActNodeQueryInterface
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
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return sprintf(
            'The ACT node of the route "%s" defined by the library "%s"',
            $this->routeName,
            $this->libraryName
        );
    }

    /**
     * Fetches the name of the library to query the route from
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the name of the route within the library
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
