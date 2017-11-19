<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Act;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Class ExpressionListNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionListNode extends AbstractActNode
{
    const TYPE = 'expression-list';

    /**
     * @var ExpressionNodeInterface[]
     */
    private $expressionNodes;

    /**
     * @param ExpressionNodeInterface[] $expressionNodes
     */
    public function __construct(array $expressionNodes)
    {
        $this->expressionNodes = $expressionNodes;
    }

    /**
     * Returns a type that represents all possible return types for the elements in the list
     * (eg. if all elements could only evaluate to NumberExpressions,
     *      then this would return StaticType<NumberExpression>. If one element
     *      could evaluate to a TextExpression, then it would return
     *      MultipleType<NumberExpression, TextExpression>)
     *
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function getElementResultType(ValidationContextInterface $validationContext)
    {
        /** @var TypeInterface|null $resultType */
        $resultType = null;

        foreach ($this->expressionNodes as $expressionNode) {
            $elementResultType = $expressionNode->getResultType($validationContext);

            if ($resultType === null) {
                $resultType = $elementResultType;
            } else {
                $resultType = $resultType->mergeWith($elementResultType);
            }
        }

        // An expression list should never be empty, so this should never return null
        return $resultType;
    }

    /**
     * Fetches all expressions in this list
     *
     * @return ExpressionNodeInterface[]
     */
    public function getExpressions()
    {
        return $this->expressionNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        foreach ($this->expressionNodes as $expressionNode) {
            $expressionNode->validate($subValidationContext);
        }
    }
}
