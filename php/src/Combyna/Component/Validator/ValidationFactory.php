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

use Combyna\Component\Behaviour\BehaviourFactoryInterface;
use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Behaviour\Validation\Validator\BehaviourSpecValidatorInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Validation\Context\AssuredSubValidationContext;
use Combyna\Component\Expression\Validation\Context\ScopeSubValidationContext;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ActNodeSubValidationContext;
use Combyna\Component\Validator\Context\DetachedSubValidationContext;
use Combyna\Component\Validator\Context\Factory\DelegatingSubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\RootSubValidationContext;
use Combyna\Component\Validator\Context\RootSubValidationContextInterface;
use Combyna\Component\Validator\Context\RootValidationContext;
use Combyna\Component\Validator\Context\RootValidationContextInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContext;
use Combyna\Component\Validator\Violation\DivisionByZeroViolation;
use Combyna\Component\Validator\Violation\GenericViolation;
use Combyna\Component\Validator\Violation\TypeMismatchViolation;

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
     * @var BehaviourFactoryInterface
     */
    private $behaviourFactory;

    /**
     * @var DelegatingSubValidationContextFactoryInterface
     */
    private $subValidationContextFactory;

    /**
     * @param DelegatingSubValidationContextFactoryInterface $subValidationContextFactory
     * @param BehaviourFactoryInterface $behaviourFactory
     */
    public function __construct(
        DelegatingSubValidationContextFactoryInterface $subValidationContextFactory,
        BehaviourFactoryInterface $behaviourFactory
    ) {
        $this->behaviourFactory = $behaviourFactory;
        $this->subValidationContextFactory = $subValidationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createActNodeContext(
        SubValidationContextInterface $parentContext,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new ActNodeSubValidationContext($parentContext, $actNode, $behaviourSpec, $subjectNode);
    }

    /**
     * {@inheritdoc}
     */
    public function createAssuredContext(
        SubValidationContextInterface $parentContext,
        ActNodeInterface $guardExpressionNode,
        BehaviourSpecInterface $guardExpressionNodeBehaviourSpec,
        array $assuranceNodes,
        ActNodeInterface $subjectNode
    ) {
        return new AssuredSubValidationContext(
            $parentContext,
            $guardExpressionNode,
            $guardExpressionNodeBehaviourSpec,
            $assuranceNodes,
            $subjectNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createContext(
        RootValidationContextInterface $rootValidationContext,
        SubValidationContextInterface $subValidationContext,
        BehaviourSpecValidatorInterface $behaviourSpecValidator
    ) {
        return new ValidationContext(
            $this,
            $this->behaviourFactory,
            $this->subValidationContextFactory,
            $behaviourSpecValidator,
            $rootValidationContext,
            $subValidationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createDetachedContext(
        SubValidationContextInterface $parentContext,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new DetachedSubValidationContext($parentContext, $actNode, $behaviourSpec, $subjectNode);
    }

    /**
     * {@inheritdoc}
     */
    public function createRootContext(
        RootSubValidationContextInterface $rootSubValidationContext,
        BehaviourSpecInterface $rootNodeBehaviourSpec,
        BehaviourSpecValidatorInterface $behaviourSpecValidator
    ) {
        return new RootValidationContext(
            $this,
            $this->behaviourFactory,
            $rootSubValidationContext,
            $rootNodeBehaviourSpec,
            $behaviourSpecValidator
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createRootSubContext(
        ActNodeInterface $rootNode,
        BehaviourSpecInterface $rootNodeBehaviourSpec,
        StructuredNodeInterface $subjectNode
    ) {
        return new RootSubValidationContext($rootNode, $rootNodeBehaviourSpec, $subjectNode);
    }

    /**
     * {@inheritdoc}
     */
    public function createScopeContext(
        SubValidationContextInterface $parentContext,
        array $variableTypeDeterminers,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new ScopeSubValidationContext(
            $parentContext,
            $variableTypeDeterminers,
            $actNode,
            $behaviourSpec,
            $subjectNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createDivisionByZeroViolation(
        SubValidationContextInterface $validationContext
    ) {
        return new DivisionByZeroViolation($validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function createGenericViolation(
        $description,
        SubValidationContextInterface $validationContext
    ) {
        return new GenericViolation($description, $validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function createTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        SubValidationContextInterface $validationContext,
        $contextDescription
    ) {
        return new TypeMismatchViolation($expectedType, $actualType, $validationContext, $contextDescription);
    }
}
