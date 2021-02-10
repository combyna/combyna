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

use Combyna\Component\Bag\Validation\Context\Specifier\ExpressionBagContextSpecifier;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\Config\Act\AbstractExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Router\Expression\RouteUrlExpression;
use Combyna\Component\Router\Type\Exotic\RouteArgumentsExoticTypeDeterminer;
use Combyna\Component\Router\Type\Exotic\RouteNameExoticTypeDeterminer;
use Combyna\Component\Router\Type\Exotic\RouterExoticTypeDeterminerFactory;
use Combyna\Component\Validator\Type\ExoticTypeDeterminer;
use Combyna\Component\Validator\Type\StaticTypeDeterminer;

/**
 * Class RouteUrlExpressionNode
 *
 * Builds a URL for a route
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteUrlExpressionNode extends AbstractExpressionNode
{
    const TYPE = RouteUrlExpression::TYPE;
    const ARGUMENTS_PARAMETER = 'arguments';
    const ROUTE_PARAMETER = 'route';

    /**
     * @var ExpressionNodeInterface
     */
    private $argumentStructureExpressionNode;

    /**
     * @var ExpressionNodeInterface
     */
    private $nameExpressionNode;

    /**
     * @param ExpressionNodeInterface $nameExpressionNode
     * @param ExpressionNodeInterface|null $argumentStructureExpressionNode
     */
    public function __construct(
        ExpressionNodeInterface $nameExpressionNode,
        ExpressionNodeInterface $argumentStructureExpressionNode = null
    ) {
        $this->argumentStructureExpressionNode = $argumentStructureExpressionNode;
        $this->nameExpressionNode = $nameExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->defineValidationContext(new ExpressionBagContextSpecifier([
            self::ROUTE_PARAMETER => $this->nameExpressionNode,
            self::ARGUMENTS_PARAMETER => $this->argumentStructureExpressionNode
        ]));

        $specBuilder->addChildNode($this->nameExpressionNode);

        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->nameExpressionNode,
                // Allow any valid route name to be provided, either as a valued static text
                // string containing a fully-qualified route library & name, or by providing
                // another route_name exotic
                new ExoticTypeDeterminer(RouteNameExoticTypeDeterminer::NAME, [
                    RouterExoticTypeDeterminerFactory::ROUTE_ARGUMENT_STRUCTURE_STATIC_CONFIG_NAME => self::ARGUMENTS_PARAMETER
                ]),
                'route name expression'
            )
        );

        if ($this->argumentStructureExpressionNode !== null) {
            $specBuilder->addChildNode($this->argumentStructureExpressionNode);

            $specBuilder->addConstraint(
                new ResultTypeConstraint(
                    $this->argumentStructureExpressionNode,
                    // Allow any valid route arguments to be provided, either as a valued static structure
                    // containing a valid set of arguments for the route, or by providing another route_arguments exotic
                    new ExoticTypeDeterminer(RouteArgumentsExoticTypeDeterminer::NAME, [
                        RouterExoticTypeDeterminerFactory::ROUTE_STATIC_CONFIG_NAME => self::ROUTE_PARAMETER
                    ]),
                    'route arguments expression'
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        // URLs are always strings
        return new StaticTypeDeterminer(TextExpression::class);
    }

    /**
     * Fetches the expression that should result in a structure of the route parameter arguments
     *
     * @return ExpressionNodeInterface|null
     */
    public function getRouteArgumentStructureExpression()
    {
        return $this->argumentStructureExpressionNode;
    }

    /**
     * Fetches the expression to evaluate for the route name (including library)
     *
     * @return ExpressionNodeInterface
     */
    public function getRouteNameExpression()
    {
        return $this->nameExpressionNode;
    }
}
