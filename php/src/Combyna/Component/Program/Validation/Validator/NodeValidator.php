<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program\Validation\Validator;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\Behaviour\BehaviourFactoryInterface;
use Combyna\Component\Behaviour\Compiler\BehaviourSpecTreeCompilerInterface;
use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Behaviour\Validation\Validator\BehaviourSpecValidatorInterface;
use Combyna\Component\Validator\Config\Act\DetachedNode;
use Combyna\Component\Validator\Constraint\DelegatingConstraintValidatorInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;

/**
 * Class NodeValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NodeValidator implements NodeValidatorInterface
{
    /**
     * @var BehaviourFactoryInterface
     */
    private $behaviourFactory;

    /**
     * @var BehaviourSpecTreeCompilerInterface
     */
    private $behaviourSpecTreeCompiler;

    /**
     * @var BehaviourSpecValidatorInterface
     */
    private $behaviourSpecValidator;

    /**
     * @var DelegatingConstraintValidatorInterface
     */
    private $delegatingConstraintValidator;

    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param BehaviourFactoryInterface $behaviourFactory
     * @param ValidationFactoryInterface $validationFactory
     * @param DelegatingConstraintValidatorInterface $delegatingConstraintValidator
     * @param BehaviourSpecTreeCompilerInterface $behaviourSpecTreeCompiler
     * @param BehaviourSpecValidatorInterface $behaviourSpecValidator
     */
    public function __construct(
        BehaviourFactoryInterface $behaviourFactory,
        ValidationFactoryInterface $validationFactory,
        DelegatingConstraintValidatorInterface $delegatingConstraintValidator,
        BehaviourSpecTreeCompilerInterface $behaviourSpecTreeCompiler,
        BehaviourSpecValidatorInterface $behaviourSpecValidator
    ) {
        $this->behaviourFactory = $behaviourFactory;
        $this->behaviourSpecTreeCompiler = $behaviourSpecTreeCompiler;
        $this->behaviourSpecValidator = $behaviourSpecValidator;
        $this->delegatingConstraintValidator = $delegatingConstraintValidator;
        $this->validationFactory = $validationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(StructuredNodeInterface $node, AppNode $appNode)
    {
        $environmentNode = $appNode->getEnvironment();
        $environmentBehaviourSpecBuilder = $this->behaviourFactory->createBehaviourSpecBuilder($environmentNode);

        $environmentNode->buildBehaviourSpec($environmentBehaviourSpecBuilder);
        // Add the app as a child for the purposes of validation,
        // as the app can reference resources from the environment but not vice versa
        $environmentBehaviourSpecBuilder->addChildNode($appNode);
        $environmentNodeBehaviourSpec = $environmentBehaviourSpecBuilder->build();

        // Compile the behaviour spec tree, running any behaviour spec passes against it
        $this->behaviourSpecTreeCompiler->compile($environmentNodeBehaviourSpec);

        // Extract the target node's behaviour spec from the environment+app spec tree, if present
        $nodeBehaviourSpec = $environmentNodeBehaviourSpec->getDescendantSpecForNode($node);

        if ($nodeBehaviourSpec === null) {
            // Otherwise if the target node is detached from the app, create it separately
            $environmentBehaviourSpecBuilder->addChildNode(new DetachedNode($node));
            $environmentNodeBehaviourSpec = $environmentBehaviourSpecBuilder->build();

            // Compile the behaviour spec tree, running any behaviour spec passes against it
            $this->behaviourSpecTreeCompiler->compile($environmentNodeBehaviourSpec);
        }

        $rootSubValidationContext = $this->validationFactory->createRootSubContext(
            $environmentNode,
            $environmentNodeBehaviourSpec
        );

        $rootValidationContext = $this->validationFactory->createRootContext(
            $rootSubValidationContext,
            $environmentNodeBehaviourSpec,
            $this->behaviourSpecValidator
        );

        // Validate the environment, which should include the specific node we're validating (usually the app)
        // because if it was a detached node, it will have been added under a special DetachedNode above
        $this->behaviourSpecValidator->validate(
            $environmentNodeBehaviourSpec,
            $rootValidationContext
        );

        return $rootValidationContext;
    }
}
