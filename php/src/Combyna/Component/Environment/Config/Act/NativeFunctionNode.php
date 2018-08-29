<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Environment\Exception\NativeFunctionNotInstalledException;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\Validation\Constraint\ResolvedTypeConstraint;
use Combyna\Component\Validator\Constraint\CallbackConstraint;
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
     * @var callable|null
     */
    private $callable = null;

    /**
     * @var string
     */
    private $functionName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var FixedStaticBagModelNode
     */
    private $parameterBagModelNode;

    /**
     * @var TypeInterface
     */
    private $returnType;

    /**
     * @param string $libraryName
     * @param string $functionName
     * @param FixedStaticBagModelNode $parameterBagModelNode
     * @param TypeInterface $returnType
     */
    public function __construct(
        $libraryName,
        $functionName,
        FixedStaticBagModelNode $parameterBagModelNode,
        TypeInterface $returnType
    ) {
        $this->functionName = $functionName;
        $this->libraryName = $libraryName;
        $this->parameterBagModelNode = $parameterBagModelNode;
        $this->returnType = $returnType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->parameterBagModelNode);

        $specBuilder->addConstraint(
            new CallbackConstraint(
                function (ValidationContextInterface $validationContext) {
                    if ($this->callable === null) {
                        $validationContext->addGenericViolation(
                            'Native function "' . $this->functionName . '" for library "' .
                            $this->libraryName . '" was never installed'
                        );
                    }
                }
            )
        );

        // Make sure the return type is a resolved, valid type
        $specBuilder->addConstraint(new ResolvedTypeConstraint($this->returnType));
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::TYPE . ':' . $this->functionName;
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
     * Fetches the native function callable set for this node
     *
     * @return callable
     * @throws NativeFunctionNotInstalledException Throws when native function was never installed
     */
    public function getCallable()
    {
        if ($this->callable === null) {
            throw new NativeFunctionNotInstalledException($this->libraryName, $this->functionName);
        }

        return $this->callable;
    }

    /**
     * Fetches the model for the parameters the function expects
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
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return true;
    }

    /**
     * Sets the native function that this node references. If the native function
     * that this node references is never set, then it will fail validation
     *
     * @param callable $callable
     */
    public function setNativeFunctionCallable(callable $callable)
    {
        $this->callable = $callable;
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
