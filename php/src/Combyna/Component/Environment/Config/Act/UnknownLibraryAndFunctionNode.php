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
 * Class UnknownLibraryAndFunctionNode
 *
 * Indicates that a referenced library (and therefore also the specified function) do not exist
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownLibraryAndFunctionNode extends AbstractActNode implements FunctionNodeInterface
{
    const TYPE = 'unknown-library-and-function';

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
        // Library and function are both unknown, so we don't know
        // what the function's return type could be
        return new UnknownType();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $validationContext->addGenericViolation(
            'Neither the library "' . $this->libraryName . '" nor its function "' . $this->functionName . '" exist'
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
