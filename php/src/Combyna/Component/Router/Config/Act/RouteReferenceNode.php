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

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;

/**
 * Class RouteReferenceNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteReferenceNode extends AbstractActNode
{
    const TYPE = 'route-reference';

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
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new RouteExistsConstraint(
                $this->libraryName,
                $this->routeName
            )
        );
    }

    /**
     * Fetches the unique name of the library that defines this route
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the unique name of the route
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }
}
