<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Context\Factory;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Validation\Context\ScopeSubValidationContextInterface;
use Combyna\Component\Expression\Validation\Context\Specifier\ScopeContextSpecifier;
use Combyna\Component\Validator\Context\Factory\SubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;

/**
 * Class ScopeContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ScopeContextFactory implements SubValidationContextFactoryInterface
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
     * Creates a ScopeSubValidationContext
     *
     * @param ScopeContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param ActNodeInterface $actNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return ScopeSubValidationContextInterface
     */
    public function createScopeContext(
        ScopeContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
        $variableTypeDeterminers = $specifier->getVariableTypeDeterminers();

        return $this->validationFactory->createScopeContext(
            $parentContext,
            $variableTypeDeterminers,
            $actNode,
            $behaviourSpec
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            ScopeContextSpecifier::class => [$this, 'createScopeContext']
        ];
    }
}
