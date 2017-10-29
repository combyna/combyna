<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Store\Config\Act;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class QueryNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class QueryNode extends AbstractActNode
{
    const TYPE = 'query';

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
     * Fetches the expression to evaluate for the result of this query
     *
     * @return ExpressionNodeInterface
     */
    public function getExpression()
    {
        return $this->expressionNode;
    }

    /**
     * Fetches the unique name of this query within its store
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fetches the model for parameters to this query
     *
     * @return FixedStaticBagModelNode
     */
    public function getParameterBagModel()
    {
        return $this->parameterBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->expressionNode->validate($subValidationContext);
        $this->parameterBagModelNode->validate($subValidationContext);
    }
}
