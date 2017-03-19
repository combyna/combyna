<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator;

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ActNodeValidationContext;
use Combyna\Component\Validator\Context\AssuredValidationContext;
use Combyna\Component\Validator\Context\GenericValidationContext;
use Combyna\Component\Validator\Context\GenericValidationContextInterface;
use Combyna\Component\Validator\Context\RootGenericValidationContext;
use Combyna\Component\Validator\Context\RootValidationContext;
use Combyna\Component\Validator\Context\ScopeValidationContext;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Violation\DivisionByZeroViolation;
use Combyna\Component\Validator\Violation\GenericViolation;
use Combyna\Component\Validator\Violation\TypeMismatchViolation;
use Combyna\Component\Validator\Violation\UndefinedAssuredStaticViolation;

/**
 * Class ValidationFactory
 *
 * Creates validation contexts and constraint violations
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidationFactory implements ValidationFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createActNodeContext(
        GenericValidationContextInterface $parentGenericContext,
        ActNodeInterface $actNode
    ) {
        $genericContext = new GenericValidationContext(
            $this,
            $parentGenericContext,
            [],
            $actNode
        );

        return new ActNodeValidationContext($genericContext);
    }

    /**
     * {@inheritdoc}
     */
    public function createAssuredContext(
        GenericValidationContextInterface $parentGenericContext,
        array $assuranceNodes
    ) {
        $genericContext = new GenericValidationContext(
            $this,
            $parentGenericContext,
            $assuranceNodes,
            null
        );

        return new AssuredValidationContext($genericContext);
    }

    /**
     * {@inheritdoc}
     */
    public function createRootContext(EnvironmentNode $environmentNode)
    {
        $genericContext = new RootGenericValidationContext($this, $environmentNode);

        return new RootValidationContext($genericContext);
    }

    /**
     * {@inheritdoc}
     */
    public function createScopeContext(
        GenericValidationContextInterface $parentGenericContext
    ) {
        $genericContext = new GenericValidationContext(
            $this,
            $parentGenericContext,
            [],
            null
        );

        return new ScopeValidationContext($genericContext);
    }

    /**
     * {@inheritdoc}
     */
    public function createDivisionByZeroViolation(
        ValidationContextInterface $validationContext
    ) {
        return new DivisionByZeroViolation($validationContext);
    }

    public function createGenericViolation(
        $description,
        ValidationContextInterface $validationContext
    ) {
        return new GenericViolation($description, $validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function createTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        ValidationContextInterface $validationContext,
        $contextDescription
    ) {
        return new TypeMismatchViolation($expectedType, $actualType, $validationContext, $contextDescription);
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function createUndefinedAssuredStaticViolation(
//        $assuredStaticName,
//        ValidationContextInterface $validationContext
//    ) {
//        return new UndefinedAssuredStaticViolation($assuredStaticName, $validationContext);
//    }
}
