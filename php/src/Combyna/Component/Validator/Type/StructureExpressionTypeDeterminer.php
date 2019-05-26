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
use Combyna\Component\Expression\Config\Act\StructureExpressionNode;
use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class StructureExpressionTypeDeterminer
 *
 * Infers a type for a static structure from a structure expression, if the expression is pure
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StructureExpressionTypeDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'structure-expression';

    /**
     * @var StaticStructureType|null
     */
    private $determinedType;

    /**
     * @var StructureExpressionNode
     */
    private $structureExpressionNode;

    /**
     * @param StructureExpressionNode $structureExpressionNode
     */
    public function __construct(
        StructureExpressionNode $structureExpressionNode
    ) {
        $this->structureExpressionNode = $structureExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        if ($this->determinedType === null) {
            // Fetch a type for the structure of the structure expression, excluding any value information
            $structureType = $this->structureExpressionNode->getImpureResultTypeDeterminer()
                ->determine($validationContext);

            // Attempt to validate the structure as a "pure" one (with no function calls,
            // widget attribute fetches etc.) - if it is then we can evaluate it to a static value
            // statically and wrap it in a ValuedType in order to perform static analysis with it
            $structureType = $validationContext->wrapInValuedTypeIfPureExpression(
                $structureType,
                $this->structureExpressionNode
            );

            $this->determinedType = $structureType;
        }

        return $this->determinedType;
    }

    /**
     * {@inheritdoc}
     */
    public function getStructuredChildNodes()
    {
        return [
            $this->structureExpressionNode
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $this->structureExpressionNode->getImpureResultTypeDeterminer()->makesQuery($querySpecifier);
    }
}
