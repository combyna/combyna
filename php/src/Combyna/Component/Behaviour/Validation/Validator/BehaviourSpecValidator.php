<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Behaviour\Validation\Validator;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Validator\Constraint\DelegatingConstraintValidatorInterface;
use Combyna\Component\Validator\Context\RootValidationContextInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;

/**
 * Class BehaviourSpecValidator
 *
 * Validates behaviour specifications
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BehaviourSpecValidator implements BehaviourSpecValidatorInterface
{
    /**
     * @var DelegatingConstraintValidatorInterface
     */
    private $delegatingConstraintValidator;

    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param ValidationFactoryInterface $validationFactory
     * @param DelegatingConstraintValidatorInterface $delegatingConstraintValidator
     */
    public function __construct(
        ValidationFactoryInterface $validationFactory,
        DelegatingConstraintValidatorInterface $delegatingConstraintValidator
    ) {
        $this->delegatingConstraintValidator = $delegatingConstraintValidator;
        $this->validationFactory = $validationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(
        BehaviourSpecInterface $behaviourSpec,
        RootValidationContextInterface $rootValidationContext
    ) {
        $rootSubValidationContext = $rootValidationContext->getRootSubValidationContext();

        $this->validateSpec(
            $behaviourSpec,
            $rootValidationContext,
            $rootSubValidationContext
                ->getBehaviourSpec()
                ->getSubValidationContextForDescendant(
                    $behaviourSpec->getSubjectOwnerNode(),
                    $rootSubValidationContext
                )
                ->getParentContext()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateSpec(
        BehaviourSpecInterface $behaviourSpec,
        RootValidationContextInterface $rootValidationContext,
        SubValidationContextInterface $parentValidationContext
    ) {
        $subActNodeContext = $behaviourSpec->getSubValidationContext($parentValidationContext);

        $validationContext = $this->validationFactory->createContext(
            $rootValidationContext,
            $subActNodeContext,
            $this
        );

        foreach ($behaviourSpec->getConstraints() as $constraint) {
            $this->delegatingConstraintValidator->validate(
                $constraint,
                $validationContext
            );
        }

        foreach ($behaviourSpec->getChildSpecs() as $childSpec) {
            $this->validateSpec(
                $childSpec,
                $rootValidationContext,
                $subActNodeContext
            );
        }
    }
}
