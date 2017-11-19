<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Act;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Class FixedStaticDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticDefinitionNode extends AbstractActNode
{
    const TYPE = 'fixed-static-definition';

    /**
     * @var ExpressionNodeInterface|null
     */
    private $defaultExpressionNode;

    /**
     * @var string
     */
    private $name;

    /**
     * @var TypeInterface
     */
    private $staticType;

    /**
     * @param string $name
     * @param TypeInterface $staticType
     * @param ExpressionNodeInterface|null $defaultExpressionNode
     */
    public function __construct(
        $name,
        TypeInterface $staticType,
        ExpressionNodeInterface $defaultExpressionNode = null
    ) {
        $this->defaultExpressionNode = $defaultExpressionNode;
        $this->name = $name;
        $this->staticType = $staticType;
    }

    /**
     * Fetches the expression evaluated as the default value for this static, if set
     *
     * @return ExpressionNodeInterface|null
     */
    public function getDefaultExpression()
    {
        return $this->defaultExpressionNode;
    }

    /**
     * Fetches the name for this static in its bag
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fetches the type that a value of this static must match
     *
     * @return TypeInterface
     */
    public function getStaticType()
    {
        return $this->staticType;
    }

    /**
     * Determines whether this static must be defined in the bag or not
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->defaultExpressionNode === null;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        if ($this->defaultExpressionNode) {
            $this->defaultExpressionNode->validate($subValidationContext);
        }
    }

    /**
     * Checks that the provided expression evaluates to a static
     * that is compatible with this definition's type
     *
     * @param ExpressionNodeInterface $expressionNode
     * @param ValidationContextInterface $validationContext
     * @param string $contextDescription
     */
    public function validateExpression(
        ExpressionNodeInterface $expressionNode,
        ValidationContextInterface $validationContext,
        $contextDescription
    ) {
        if (!$this->staticType->allows($expressionNode->getResultType($validationContext))) {
            $validationContext->addTypeMismatchViolation(
                $this->staticType,
                $expressionNode->getResultType($validationContext),
                $contextDescription . ' ' . $this->name
            );
        }
    }
}
