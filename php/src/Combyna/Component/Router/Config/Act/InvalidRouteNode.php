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

use Combyna\Component\Bag\Config\Act\DynamicUnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

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
        $this->libraryName = $libraryName;
        $this->routeName = $routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
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
    public function getName()
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
    public function getParameterBagModel(QueryRequirementInterface $queryRequirement)
    {
        return new DynamicUnknownFixedStaticBagModelNode(
            sprintf(
                'Parameter bag model for route "%s" of library "%s"',
                $this->routeName,
                $this->libraryName
            ),
            $queryRequirement
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
