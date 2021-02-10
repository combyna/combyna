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

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Bag\Config\Act\FixedStaticDefinitionNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\StructureExpression;
use Combyna\Component\Validator\Type\StaticStructureTypeDeterminer;
use Combyna\Component\Validator\Type\StructureExpressionTypeDeterminer;

/**
 * Class StructureExpressionNode
 *
 * Contains a list of attributes
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StructureExpressionNode extends AbstractExpressionNode
{
    const TYPE = StructureExpression::TYPE;

    /**
     * @var ExpressionBagNode
     */
    private $expressionBagNode;

    /**
     * @param ExpressionBagNode $expressionBagNode
     */
    public function __construct(ExpressionBagNode $expressionBagNode)
    {
        $this->expressionBagNode = $expressionBagNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->expressionBagNode);
    }

    /**
     * Fetches the bag of expressions that will form the attributes of the structure
     *
     * @return ExpressionBagNode
     */
    public function getExpressionBag()
    {
        return $this->expressionBagNode;
    }

    /**
     * Fetches a type determiner for this structure, excluding any value information
     *
     * @return StaticStructureTypeDeterminer
     */
    public function getImpureResultTypeDeterminer()
    {
        $staticDefinitionNodes = [];

        foreach ($this->expressionBagNode->getExpressions() as $attributeName => $expressionNode) {
            $staticDefinitionNodes[$attributeName] = new FixedStaticDefinitionNode(
                $attributeName,
                $expressionNode->getResultTypeDeterminer()
            );
        }

        $attributeBagModelNode = new FixedStaticBagModelNode($staticDefinitionNodes);

        return new StaticStructureTypeDeterminer($attributeBagModelNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new StructureExpressionTypeDeterminer($this);
    }
}
