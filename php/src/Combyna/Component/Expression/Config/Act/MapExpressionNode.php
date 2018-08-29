<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\MapExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Expression\Validation\Context\Specifier\ScopeContextSpecifier;
use Combyna\Component\Type\AnyType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Type\ListElementTypeDeterminer;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;
use Combyna\Component\Validator\Type\StaticListTypeDeterminer;

/**
 * Class MapExpressionNode
 *
 * Evaluates a list of expressions, mapping their static values to a second list of statics
 * using the result of evaluating a specific mapping expression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MapExpressionNode extends AbstractExpressionNode
{
    const TYPE = MapExpression::TYPE;

    /**
     * @var string|null
     */
    private $indexVariableName;

    /**
     * @var string
     */
    private $itemVariableName;

    /**
     * @var ExpressionNodeInterface
     */
    private $listExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $mapExpression;

    /**
     * @param ExpressionNodeInterface $listExpression
     * @param string $itemVariableName
     * @param string|null $indexVariableName
     * @param ExpressionNodeInterface $mapExpression
     */
    public function __construct(
        ExpressionNodeInterface $listExpression,
        $itemVariableName,
        $indexVariableName,
        ExpressionNodeInterface $mapExpression
    ) {
        $this->indexVariableName = $indexVariableName;
        $this->itemVariableName = $itemVariableName;
        $this->listExpression = $listExpression;
        $this->mapExpression = $mapExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->listExpression);

        // Validate the map expression in a sub-context that has access to the item and/or index vars
        $specBuilder->addSubSpec(function (BehaviourSpecBuilderInterface $subSpecBuilder) {
            $scopeContextSpecifier = new ScopeContextSpecifier();
            $scopeContextSpecifier->defineVariable(
                $this->itemVariableName,
                new ListElementTypeDeterminer($this->listExpression->getResultTypeDeterminer())
            );

            if ($this->indexVariableName !== null) {
                $scopeContextSpecifier->defineVariable(
                    $this->indexVariableName,
                    new PresolvedTypeDeterminer(new StaticType(NumberExpression::class))
                );
            }

            $subSpecBuilder->defineValidationContext($scopeContextSpecifier);

            $subSpecBuilder->addChildNode($this->mapExpression);
        });

        // Ensure the list operand can only ever evaluate to a list
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->listExpression,
                new StaticListType(new AnyType()),
                'list operand'
            )
        );
    }

    /**
     * Fetches the name of the variable to define in the context with the current element's index
     *
     * @return string|null
     */
    public function getIndexVariableName()
    {
        return $this->indexVariableName;
    }

    /**
     * Fetches the name of the variable to define in the context with the current element's static value
     *
     * @return string
     */
    public function getItemVariableName()
    {
        return $this->itemVariableName;
    }

    /**
     * Fetches the expression to use to fetch the original list
     *
     * @return ExpressionNodeInterface
     */
    public function getListExpression()
    {
        return $this->listExpression;
    }

    /**
     * Fetches the expression to use to map each element in the original list to the resultant one
     *
     * @return ExpressionNodeInterface
     */
    public function getMapExpression()
    {
        return $this->mapExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        // The map expression will be evaluated for each element, so the resulting static's type
        // will be a static list with the map expression as the element type
        return new StaticListTypeDeterminer($this->mapExpression->getResultTypeDeterminer());
    }
}
