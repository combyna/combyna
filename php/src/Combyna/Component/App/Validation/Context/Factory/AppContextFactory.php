<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Validation\Context\Factory;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\App\Validation\AppValidationFactoryInterface;
use Combyna\Component\App\Validation\Context\AppSubValidationContextInterface;
use Combyna\Component\App\Validation\Context\Specifier\AppContextSpecifier;
use Combyna\Component\Behaviour\BehaviourFactoryInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Validator\Context\Factory\SubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class AppContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppContextFactory implements SubValidationContextFactoryInterface
{
    /**
     * @var BehaviourFactoryInterface
     */
    private $behaviourFactory;

    /**
     * @var AppValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param AppValidationFactoryInterface $validationFactory
     * @param BehaviourFactoryInterface $behaviourFactory
     */
    public function __construct(
        AppValidationFactoryInterface $validationFactory,
        BehaviourFactoryInterface $behaviourFactory
    ) {
        $this->behaviourFactory = $behaviourFactory;
        $this->validationFactory = $validationFactory;
    }

    /**
     * Creates an AppSubValidationContext
     *
     * @param AppContextSpecifier $specifier
     * @param SubValidationContextInterface $rootSubContext
     * @param AppNode $appNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return AppSubValidationContextInterface
     */
    public function createAppContext(
        AppContextSpecifier $specifier,
        SubValidationContextInterface $rootSubContext,
        AppNode $appNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
//        // Build the environment's behaviour spec so we can create its sub-context,
//        // with the root sub-context as its parent
//        $environmentBehaviourSpecBuilder = $this->behaviourFactory->createBehaviourSpecBuilder(
//            $specifier->getEnvironmentNode()
//        );
//        $specifier->getEnvironmentNode()->buildBehaviourSpec($environmentBehaviourSpecBuilder);
//        $environmentBehaviourSpec = $environmentBehaviourSpecBuilder->build();
//        $environmentParentContext = $environmentBehaviourSpec->getSubValidationContext(
//            $rootSubContext
//        );
//
//        return $this->validationFactory->createAppContext($environmentParentContext, $appNode, $behaviourSpec);

        return $this->validationFactory->createAppContext($rootSubContext, $appNode, $behaviourSpec);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            AppContextSpecifier::class => [$this, 'createAppContext']
        ];
    }
}
