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
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Behaviour\Spec\SubBehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Validation\Context\Specifier\ScopeContextSpecifier;
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
     * @var FixedStaticBagModelNodeInterface
     */
    private $parameterBagModelNode;

    /**
     * @param string $name
     * @param FixedStaticBagModelNodeInterface $parameterBagModelNode
     * @param ExpressionNodeInterface $expressionNode
     */
    public function __construct(
        $name,
        FixedStaticBagModelNodeInterface $parameterBagModelNode,
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
        $specBuilder->addChildNode($this->parameterBagModelNode);

        $specBuilder->addSubSpec(function (SubBehaviourSpecBuilderInterface $subSpecBuilder) {
            $scopeContextSpecifier = new ScopeContextSpecifier();
            // Expose the query parameters to the expression as variables
            // (for some similar but special "parameters" such as signal payload statics, this is done differently -
            // for payloads for example, statics must be fetched using the builtin `signal_payload(...)`)
            $scopeContextSpecifier->defineBagStaticsAsVariables($this->parameterBagModelNode);

            $subSpecBuilder->defineValidationContext($scopeContextSpecifier);

            $subSpecBuilder->addChildNode($this->expressionNode);
        });
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
    public function getIdentifier()
    {
        return self::TYPE . ':' . $this->name;
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
