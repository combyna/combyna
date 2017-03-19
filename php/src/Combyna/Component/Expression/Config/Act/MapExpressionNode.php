<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Expression\MapExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;

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
    public function getResultType(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $this->createSubValidationContext($validationContext);

        // The map expression will be evaluated for each element, so the resulting static's type
        // will be a static list with the map expression as the element type
        $elementType = $this->mapExpression->getResultType($subValidationContext);

        return new StaticListType($elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $this->createSubValidationContext($validationContext);

        $this->listExpression->validate($subValidationContext);
        $this->mapExpression->validate($subValidationContext);

        // Ensure the list operand can only ever evaluate to a list
        // with elements that evaluate only to either text or number statics
        $subValidationContext->assertListResultType(
            $this->listExpression,
            'list operand'
        );
    }

    /**
     * Creates a sub-validation context for this expression, with the index and item
     * variables defined along with their types
     *
     * @param ValidationContextInterface $validationContext
     * @return ValidationContextInterface
     */
    private function createSubValidationContext(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext
            ->createSubActNodeContext($this)
            ->createSubScopeContext();

        $listResultType = $this->listExpression->getResultType($subValidationContext);

        if ($listResultType instanceof StaticListType) {
            // Will also ensure the item variable name is not defined by a parent context to prevent shadowing
            $subValidationContext->defineVariable(
                $this->itemVariableName,
                $listResultType->getElementType()
            );
        }

        if ($this->indexVariableName !== null) {
            // Define the variable for the index of the item in the list
            // Will also ensure the index variable name is not defined by a parent context to prevent shadowing
            $subValidationContext->defineVariable(
                $this->indexVariableName,
                new StaticType(NumberExpression::class)
            );
        }

        return $subValidationContext;
    }
}
