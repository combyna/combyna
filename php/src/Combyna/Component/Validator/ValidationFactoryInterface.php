<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Behaviour\Validation\Validator\BehaviourSpecValidatorInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodeInterface;
use Combyna\Component\Expression\Validation\Context\AssuredSubValidationContextInterface;
use Combyna\Component\Expression\Validation\Context\ScopeSubValidationContextInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ActNodeSubValidationContextInterface;
use Combyna\Component\Validator\Context\DetachedSubValidationContextInterface;
use Combyna\Component\Validator\Context\RootSubValidationContextInterface;
use Combyna\Component\Validator\Context\RootValidationContextInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;
use Combyna\Component\Validator\Violation\DivisionByZeroViolation;
use Combyna\Component\Validator\Violation\GenericViolation;
use Combyna\Component\Validator\Violation\TypeMismatchViolation;

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
     * Creates a new ActNodeSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param ActNodeInterface $actNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return ActNodeSubValidationContextInterface
     */
    public function createActNodeContext(
        SubValidationContextInterface $parentContext,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec
    );

    /**
     * Creates a new AssuredSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param ActNodeInterface $guardExpressionNode
     * @param BehaviourSpecInterface $guardExpressionNodeBehaviourSpec
     * @param AssuranceNodeInterface[] $assuranceNodes
     * @return AssuredSubValidationContextInterface
     */
    public function createAssuredContext(
        SubValidationContextInterface $parentContext,
        ActNodeInterface $guardExpressionNode,
        BehaviourSpecInterface $guardExpressionNodeBehaviourSpec,
        array $assuranceNodes
    );

    /**
     * Creates a ValidationContext
     *
     * @param RootValidationContextInterface $rootValidationContext
     * @param SubValidationContextInterface $subValidationContext
     * @param BehaviourSpecValidatorInterface $behaviourSpecValidator
     * @return ValidationContextInterface
     */
    public function createContext(
        RootValidationContextInterface $rootValidationContext,
        SubValidationContextInterface $subValidationContext,
        BehaviourSpecValidatorInterface $behaviourSpecValidator
    );

    /**
     * Creates a DetachedSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param ActNodeInterface $actNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return DetachedSubValidationContextInterface
     */
    public function createDetachedContext(
        SubValidationContextInterface $parentContext,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec
    );

    /**
     * Creates a RootValidationContext
     *
     * @param RootSubValidationContextInterface $rootSubValidationContext
     * @param BehaviourSpecInterface $rootNodeBehaviourSpec
     * @param BehaviourSpecValidatorInterface $behaviourSpecValidator
     * @return RootValidationContextInterface
     */
    public function createRootContext(
        RootSubValidationContextInterface $rootSubValidationContext,
        BehaviourSpecInterface $rootNodeBehaviourSpec,
        BehaviourSpecValidatorInterface $behaviourSpecValidator
    );

    /**
     * Creates a new RootSubValidationContext
     *
     * @param ActNodeInterface $rootNode
     * @param BehaviourSpecInterface $rootNodeBehaviourSpec
     * @return RootSubValidationContextInterface
     */
    public function createRootSubContext(
        ActNodeInterface $rootNode,
        BehaviourSpecInterface $rootNodeBehaviourSpec
    );

    /**
     * Creates a new ScopeSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param TypeDeterminerInterface[] $variableTypeDeterminers
     * @param ActNodeInterface $actNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return ScopeSubValidationContextInterface
     */
    public function createScopeContext(
        SubValidationContextInterface $parentContext,
        array $variableTypeDeterminers,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec
    );

    /**
     * Creates a new DivisionByZeroViolation
     *
     * @param SubValidationContextInterface $validationContext
     * @return DivisionByZeroViolation
     */
    public function createDivisionByZeroViolation(
        SubValidationContextInterface $validationContext
    );

    /**
     * Creates a new GenericViolation
     *
     * @param string $description
     * @param SubValidationContextInterface $validationContext
     * @return GenericViolation
     */
    public function createGenericViolation(
        $description,
        SubValidationContextInterface $validationContext
    );

    /**
     * Creates a new TypeMismatchViolation
     *
     * @param TypeInterface $expectedType
     * @param TypeInterface $actualType
     * @param SubValidationContextInterface $validationContext
     * @param string $contextDescription
     * @return TypeMismatchViolation
     */
    public function createTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        SubValidationContextInterface $validationContext,
        $contextDescription
    );
}
