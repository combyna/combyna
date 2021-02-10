<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Act\Expression;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\Config\Act\AbstractExpressionNode;
use Combyna\Component\Router\Expression\RouteArgumentExpression;
use Combyna\Component\Router\Validation\Constraint\RouteParameterExistsConstraint;
use Combyna\Component\Router\Validation\Query\CurrentViewRouteParameterTypeQuery;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class RouteArgumentExpressionNode
 *
 * Fetches an argument given for a route parameter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteArgumentExpressionNode extends AbstractExpressionNode
{
    const TYPE = RouteArgumentExpression::TYPE;

    /**
     * @var string
     */
    private $parameterName;

    /**
     * @param string $parameterName
     */
    public function __construct($parameterName)
    {
        $this->parameterName = $parameterName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(new RouteParameterExistsConstraint($this->parameterName));
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        // Fetch the type of this parameter from the route(s) that use the current view
        return new QueriedResultTypeDeterminer(
            new CurrentViewRouteParameterTypeQuery($this->parameterName),
            $this
        );
    }

    /**
     * Fetches the name of the parameter to fetch the argument for
     *
     * @return string
     */
    public function getRouteParameterName()
    {
        return $this->parameterName;
    }
}
