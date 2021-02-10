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
use Combyna\Component\Bag\Config\Act\UnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class UnknownQueryNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownQueryNode extends AbstractActNode implements DynamicActNodeInterface, QueryNodeInterface
{
    const TYPE = 'unknown-store-query';

    /**
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     */
    public function __construct($name, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->name = $name;

        $dynamicActNodeAdopter->adoptDynamicActNode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(new KnownFailureConstraint(sprintf('Unknown query node "%s"', $this->name)));

        $specBuilder->addChildNode($this->dynamicContainerNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpression()
    {
        return new UnknownExpressionNode(
            sprintf('Unknown query node "%s" expression', $this->name),
            $this->dynamicContainerNode
        );
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
        return new UnknownFixedStaticBagModelNode(
            sprintf('Unknown query node "%s" parameter bag model', $this->name),
            $this->dynamicContainerNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateArgumentExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode
    ) {
        // Nothing to do, validation should already have been marked failed by the above
    }
}
