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

use Combyna\Component\Bag\Config\Act\UnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;

/**
 * Class InvalidRouteNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InvalidRouteNode extends AbstractActNode implements RouteNodeInterface
{
    const TYPE = 'unknown-route';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

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
     * @param string $contextDescription
     */
    public function __construct($libraryName, $routeName, $contextDescription)
    {
        $this->contextDescription = $contextDescription;
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->libraryName = $libraryName;
        $this->routeName = $routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->dynamicContainerNode);

        // Make sure validation fails, because this node is invalid
        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                sprintf(
                    'Route "%s" of library "%s" is invalid: %s',
                    $this->routeName,
                    $this->libraryName,
                    $this->contextDescription
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageViewName()
    {
        return '[unknown]';
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterBagModel()
    {
        return new UnknownFixedStaticBagModelNode(
            sprintf(
                'Parameter bag model for route "%s" of library "%s"',
                $this->routeName,
                $this->libraryName
            ),
            $this->dynamicContainerNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlPattern()
    {
        return '[unknown]';
    }
}
