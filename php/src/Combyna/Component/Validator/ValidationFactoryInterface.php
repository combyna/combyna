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
use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNode;
use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodeInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Validator\Context\ActNodeValidationContextInterface;
use Combyna\Component\Validator\Context\AssuredValidationContext;
use Combyna\Component\Validator\Context\GenericValidationContextInterface;
use Combyna\Component\Validator\Context\RootValidationContextInterface;
use Combyna\Component\Validator\Context\ScopeValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Violation\DivisionByZeroViolation;
use Combyna\Component\Validator\Violation\GenericViolation;
use Combyna\Component\Validator\Violation\TypeMismatchViolation;
use Combyna\Component\Type\TypeInterface;

/**
 * Interface ValidationFactoryInterface
 *
 * Creates validation contexts and constraint violations
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ValidationFactoryInterface
{
    /**
     * Creates a new ActNodeValidationContext
     *
     * @param GenericValidationContextInterface $parentGenericContext
     * @param ActNodeInterface $actNode
     * @return ActNodeValidationContextInterface
     */
    public function createActNodeContext(
        GenericValidationContextInterface $parentGenericContext,
        ActNodeInterface $actNode
    );

    /**
     * Creates a new AssuredValidationContext
     *
     * @param GenericValidationContextInterface $parentGenericContext
     * @param AssuranceNodeInterface[] $assuranceNodes
     * @return AssuredValidationContext
     */
    public function createAssuredContext(
        GenericValidationContextInterface $parentGenericContext,
        array $assuranceNodes
    );

    /**
     * Creates a new RootValidationContext
     *
     * @param EnvironmentNode $environmentNode
     * @return RootValidationContextInterface
     */
    public function createRootContext(EnvironmentNode $environmentNode);

    /**
     * Creates a new ScopeValidationContext
     *
     * @param GenericValidationContextInterface $parentGenericContext
     * @return ScopeValidationContextInterface
     */
    public function createScopeContext(
        GenericValidationContextInterface $parentGenericContext
    );

    /**
     * Creates a new DivisionByZeroViolation
     *
     * @param ValidationContextInterface $validationContext
     * @return DivisionByZeroViolation
     */
    public function createDivisionByZeroViolation(
        ValidationContextInterface $validationContext
    );

    /**
     * Creates a new GenericViolation
     *
     * @param string $description
     * @param ValidationContextInterface $validationContext
     * @return GenericViolation
     */
    public function createGenericViolation(
        $description,
        ValidationContextInterface $validationContext
    );

    /**
     * Creates a new TypeMismatchViolation
     *
     * @param TypeInterface $expectedType
     * @param TypeInterface $actualType
     * @param ValidationContextInterface $validationContext
     * @param string $contextDescription
     * @return TypeMismatchViolation
     */
    public function createTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        ValidationContextInterface $validationContext,
        $contextDescription
    );

//    /**
//     * Creates a new UndefinedAssuredStaticViolation
//     *
//     * @param string $assuredStaticName
//     * @param ValidationContextInterface $validationContext
//     * @return UndefinedAssuredStaticViolation
//     */
//    public function createUndefinedAssuredStaticViolation(
//        $assuredStaticName,
//        ValidationContextInterface $validationContext
//    );
}
