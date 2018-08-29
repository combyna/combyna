<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Store\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class QueryNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class QueryNode extends AbstractActNode implements QueryNodeInterface
{
    const TYPE = 'store-query';

    /**
     * @var ExpressionNodeInterface
     */
    private $expressionNode;

    /**
     * @var string
     */
    private $name;

    /**
     * @var FixedStaticBagModelNode
     */
    private $parameterBagModelNode;

    /**
     * @param string $name
     * @param FixedStaticBagModelNode $parameterBagModelNode
     * @param ExpressionNodeInterface $expressionNode
     */
    public function __construct(
        $name,
        FixedStaticBagModelNode $parameterBagModelNode,
        ExpressionNodeInterface $expressionNode
    ) {
        $this->expressionNode = $expressionNode;
        $this->name = $name;
        $this->parameterBagModelNode = $parameterBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->expressionNode);
        $specBuilder->addChildNode($this->parameterBagModelNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpression()
    {
        return $this->expressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterBagModel()
    {
        return $this->parameterBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function validateArgumentExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode
    ) {
        $this->parameterBagModelNode->validateStaticExpressionBag(
            $validationContext,
            $expressionBagNode,
            'parameter'
        );
    }
}
