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
use Combyna\Component\Expression\AttributeExpression;
use Combyna\Component\Expression\Validation\Constraint\StructureHasAttributeConstraint;
use Combyna\Component\Validator\Type\StructureAttributeTypeDeterminer;

/**
 * Class AttributeExpressionNode
 *
 * Fetches an attribute of a structure
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AttributeExpressionNode extends AbstractExpressionNode
{
    const TYPE = AttributeExpression::TYPE;

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
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->structureExpressionNode);

        $specBuilder->addConstraint(
            new StructureHasAttributeConstraint(
                $this->structureExpressionNode,
                $this->attributeName
            )
        );
    }

    /**
     * Fetches the name of the attribute to fetch
     *
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new StructureAttributeTypeDeterminer(
            $this->structureExpressionNode,
            $this->attributeName
        );
    }

    /**
     * Fetches the expression to evaluate to get the structure
     * to then fetch an attribute of
     *
     * @return ExpressionNodeInterface
     */
    public function getStructure()
    {
        return $this->structureExpressionNode;
    }
}
