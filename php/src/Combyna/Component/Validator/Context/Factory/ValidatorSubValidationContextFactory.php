<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context\Factory;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Validator\Context\ActNodeSubValidationContextInterface;
use Combyna\Component\Validator\Context\DetachedSubValidationContextInterface;
use Combyna\Component\Validator\Context\Specifier\ActNodeContextSpecifier;
use Combyna\Component\Validator\Context\Specifier\DetachedContextSpecifier;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;

/**
 * Class ValidatorSubValidationContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidatorSubValidationContextFactory implements SubValidationContextFactoryInterface
{
    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param ValidationFactoryInterface $validationFactory
     */
    public function __construct(ValidationFactoryInterface $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }

    /**
     * Creates an ActNodeSubValidationContext
     *
     * @param ActNodeContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param ActNodeInterface $actNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return ActNodeSubValidationContextInterface
     */
    public function createActNodeContext(
        ActNodeContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
        return $this->validationFactory->createActNodeContext($parentContext, $actNode, $behaviourSpec);
    }

    /**
     * Creates a DetachedSubValidationContext
     *
     * @param DetachedContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param ActNodeInterface $actNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return DetachedSubValidationContextInterface
     */
    public function createDetachedContext(
        DetachedContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
        return $this->validationFactory->createDetachedContext($parentContext, $actNode, $behaviourSpec);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            ActNodeContextSpecifier::class => [$this, 'createActNodeContext'],
            DetachedContextSpecifier::class => [$this, 'createDetachedContext']
        ];
    }
}
