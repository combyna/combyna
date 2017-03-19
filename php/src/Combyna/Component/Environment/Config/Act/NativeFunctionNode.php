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
use Combyna\Component\Environment\Exception\NativeFunctionNotInstalledException;
use Combyna\Component\Environment\Library\NativeFunction;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class NativeFunctionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NativeFunctionNode extends AbstractActNode implements FunctionNodeInterface
{
    const TYPE = 'native-function';

    /**
     * @var string
     */
    private $functionName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var NativeFunction|null
     */
    private $nativeFunction = null;

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
     * Fetches the name of the library
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->functionName;
    }

    /**
     * Fetches the native function set for this node
     *
     * @return NativeFunction
     * @throws NativeFunctionNotInstalledException Throws when native function was never installed
     */
    public function getNativeFunction()
    {
        if ($this->nativeFunction === null) {
            throw new NativeFunctionNotInstalledException($this->libraryName, $this->functionName);
        }

        return $this->nativeFunction;
    }

    /**
     * Sets the native function that this node references. If the native function
     * that this node references is never set, then it will fail validation
     *
     * @param NativeFunction $nativeFunction
     */
    public function setNativeFunction(NativeFunction $nativeFunction)
    {
        $this->nativeFunction = $nativeFunction;
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnType()
    {
        if (!$this->nativeFunction) {
            return new UnknownType();
        }

        return $this->nativeFunction->getReturnType();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        if ($this->nativeFunction === null) {
            $subValidationContext->addGenericViolation(
                'Native function "' . $this->functionName . '" for library "' .
                $this->libraryName . '" was never installed'
            );

            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateArgumentExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode
    ) {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        if (!$this->nativeFunction) {
            // Native function was never installed - ::validate() will handle
            return;
        }

        $this->nativeFunction->validateArgumentExpressionBag($subValidationContext, $expressionBagNode);
    }
}
