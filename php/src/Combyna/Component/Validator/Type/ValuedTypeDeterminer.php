<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Type;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ValuedTypeDeterminer
 *
 * Defines a valued type
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValuedTypeDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'valued';

    /**
     * @var ExpressionNodeInterface
     */
    private $valueExpressionNode;

    /**
     * @var TypeDeterminerInterface
     */
    private $wrappedTypeDeterminer;

    /**
     * @param TypeDeterminerInterface $wrappedTypeDeterminer
     * @param ExpressionNodeInterface $valueExpressionNode
     */
    public function __construct(
        TypeDeterminerInterface $wrappedTypeDeterminer,
        ExpressionNodeInterface $valueExpressionNode
    ) {
        $this->valueExpressionNode = $valueExpressionNode;
        $this->wrappedTypeDeterminer = $wrappedTypeDeterminer;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        $wrappedType = $this->wrappedTypeDeterminer->determine($validationContext);

        // Check that the value expression is pure before trying to evaluate it
        $expressionRootValidationContext = $validationContext->validateActNodeInIsolation($this->valueExpressionNode);

        if ($expressionRootValidationContext->isViolated()) {
            return new UnresolvedType('Impure value expression given for valued type');
        }

        return $validationContext->wrapInValuedType($wrappedType, $this->valueExpressionNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getStructuredChildNodes()
    {
        return [
            // Ensure the value expression is validated in the main context too
            $this->valueExpressionNode
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $this->wrappedTypeDeterminer->makesQuery($querySpecifier) ||
            $this->valueExpressionNode->makesQuery($querySpecifier);
    }
}
