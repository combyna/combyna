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
use Combyna\Component\Expression\Config\Act\ListExpressionNode;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ListExpressionTypeDeterminer
 *
 * Infers a type for a static list from a list expression, if the expression is pure
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ListExpressionTypeDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'list-expression';

    /**
     * @var StaticListType|null
     */
    private $determinedType;

    /**
     * @var ListExpressionNode
     */
    private $listExpressionNode;

    /**
     * @param ListExpressionNode $listExpressionNode
     */
    public function __construct(
        ListExpressionNode $listExpressionNode
    ) {
        $this->listExpressionNode = $listExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        if ($this->determinedType === null) {
            $listExpressionNodeValidationContext = $validationContext->createValidationContextForActNode(
                $this->listExpressionNode
            );

            // Fetch a type for the structure of the list expression, excluding any value information
            $listType = $this->listExpressionNode->getImpureResultTypeDeterminer()
                ->determine($listExpressionNodeValidationContext);

            // Attempt to validate the list as a "pure" one (with no function calls,
            // widget attribute fetches etc.) - if it is then we can evaluate it to a static value
            // statically and wrap it in a ValuedType in order to perform static analysis with it
            $listType = $listExpressionNodeValidationContext->wrapInValuedTypeIfPureExpression(
                $listType,
                $this->listExpressionNode
            );

            $this->determinedType = $listType;
        }

        return $this->determinedType;
    }

    /**
     * {@inheritdoc}
     */
    public function getStructuredChildNodes()
    {
        return [
            $this->listExpressionNode
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $this->listExpressionNode->getImpureResultTypeDeterminer()->makesQuery($querySpecifier);
    }
}
