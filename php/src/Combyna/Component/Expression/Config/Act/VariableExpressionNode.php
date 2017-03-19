<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Expression\VariableExpression;

/**
 * Class VariableExpressionNode
 *
 * Returns the static value of a variable in the current context
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class VariableExpressionNode extends AbstractExpressionNode
{
    const TYPE = VariableExpression::TYPE;

    /**
     * @var string
     */
    private $variableName;

    /**
     * @param string $variableName
     */
    public function __construct($variableName)
    {
        $this->variableName = $variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return $validationContext->getVariableType($this->variableName);
    }

    /**
     * Fetches the name of the variable this expression should fetch
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $subValidationContext->assertVariableExists($this->variableName);
    }
}
