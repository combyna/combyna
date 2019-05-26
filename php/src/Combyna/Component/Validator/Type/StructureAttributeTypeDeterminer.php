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
use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Type\ValuedType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class StructureAttributeTypeDeterminer
 *
 * Fetches the type of an attribute of a structure model
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StructureAttributeTypeDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'structure-attribute';

    /**
     * @var string
     */
    private $attributeName;

    /**
     * @var ExpressionNodeInterface
     */
    private $structureExpressionNode;

    /**
     * @param ExpressionNodeInterface $structureExpressionNode
     * @param string $attributeName
     */
    public function __construct(ExpressionNodeInterface $structureExpressionNode, $attributeName)
    {
        $this->attributeName = $attributeName;
        $this->structureExpressionNode = $structureExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        $structureType = $validationContext->getExpressionResultType($this->structureExpressionNode);

        if ($structureType instanceof ValuedType) {
            $structureType = $structureType->getWrappedType();
        }

        if ($structureType instanceof StaticStructureType) {
            return $structureType->getAttributeType($this->attributeName);
        }

        // Determiner does not resolve to a structure, so we cannot fetch a type for one of its attributes
        return new UnresolvedType(sprintf('structure attribute "%s" type', $this->attributeName));
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $this->structureExpressionNode->makesQuery($querySpecifier);
    }
}
