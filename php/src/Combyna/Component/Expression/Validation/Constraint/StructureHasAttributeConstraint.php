<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Constraint;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;

/**
 * Class StructureHasAttributeConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StructureHasAttributeConstraint implements ExpressionValidationConstraintInterface
{
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
     * Fetches the name of the attribute being referenced
     *
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    /**
     * Fetches the expression to evaluate to get the structure
     *
     * @return ExpressionNodeInterface
     */
    public function getStructureExpression()
    {
        return $this->structureExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
