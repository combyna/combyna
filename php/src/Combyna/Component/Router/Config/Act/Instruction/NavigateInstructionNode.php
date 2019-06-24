<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Act\Instruction;

use Combyna\Component\Bag\Validation\Context\Specifier\ExpressionBagContextSpecifier;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Router\Type\Exotic\RouteArgumentsExoticTypeDeterminer;
use Combyna\Component\Router\Type\Exotic\RouteNameExoticTypeDeterminer;
use Combyna\Component\Router\Type\Exotic\RouterExoticTypeDeterminerFactory;
use Combyna\Component\Trigger\Config\Act\InstructionNodeInterface;
use Combyna\Component\Validator\Type\ExoticTypeDeterminer;

/**
 * Class NavigateInstructionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NavigateInstructionNode extends AbstractActNode implements InstructionNodeInterface
{
    const TYPE = 'navigate';
    const ARGUMENTS_PARAMETER = 'arguments';
    const ROUTE_PARAMETER = 'route';

    /**
     * @var ExpressionNodeInterface|null
     */
    private $routeArgumentStructureExpressionNode;

    /**
     * @var ExpressionNodeInterface
     */
    private $routeNameExpressionNode;

    /**
     * @param ExpressionNodeInterface $routeNameExpressionNode
     * @param ExpressionNodeInterface|null $routeArgumentStructureExpressionNode
     */
    public function __construct(
        ExpressionNodeInterface $routeNameExpressionNode,
        ExpressionNodeInterface $routeArgumentStructureExpressionNode = null
    ) {
        $this->routeArgumentStructureExpressionNode = $routeArgumentStructureExpressionNode;
        $this->routeNameExpressionNode = $routeNameExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->defineValidationContext(new ExpressionBagContextSpecifier([
            self::ROUTE_PARAMETER => $this->routeNameExpressionNode,
            self::ARGUMENTS_PARAMETER => $this->routeArgumentStructureExpressionNode
        ]));

        $specBuilder->addChildNode($this->routeNameExpressionNode);

        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->routeNameExpressionNode,
                // Allow any valid route name to be provided, either as a valued static text
                // string containing a fully-qualified route library & name, or by providing
                // another route_name exotic
                new ExoticTypeDeterminer(RouteNameExoticTypeDeterminer::NAME, [
                    RouterExoticTypeDeterminerFactory::ROUTE_ARGUMENT_STRUCTURE_STATIC_CONFIG_NAME => self::ARGUMENTS_PARAMETER
                ]),
                'route name expression'
            )
        );

        if ($this->routeArgumentStructureExpressionNode !== null) {
            $specBuilder->addChildNode($this->routeArgumentStructureExpressionNode);

            $specBuilder->addConstraint(
                new ResultTypeConstraint(
                    $this->routeArgumentStructureExpressionNode,
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
     * Fetches the expression to evaluate for the route's parameter arguments, if set
     *
     * @return ExpressionNodeInterface|null
     */
    public function getRouteArgumentStructureExpression()
    {
        return $this->routeArgumentStructureExpressionNode;
    }

    /**
     * Fetches the expression to evaluate to get the route to be navigated to by this instruction
     *
     * @return ExpressionNodeInterface
     */
    public function getRouteNameExpression()
    {
        return $this->routeNameExpressionNode;
    }
}
