<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class UnknownFunctionNode
 *
 * Indicates that a referenced library exists but does not define the specified function
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownFunctionNode extends AbstractActNode implements FunctionNodeInterface
{
    const TYPE = 'unknown-function';

    /**
     * @var string
     */
    private $functionName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @param string $libraryName
     * @param string $functionName
     */
    public function __construct($libraryName, $functionName)
    {
        $this->functionName = $functionName;
        $this->libraryName = $libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->functionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnType()
    {
        // We don't know what the function's return type could be as it is not defined
        return new UnknownType();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $validationContext->addGenericViolation(
            'Library "' . $this->libraryName . '" does not support function "' . $this->functionName . '"'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateArgumentExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode
    ) {
        // Nothing to do - validation of this node itself will always fail
    }
}
