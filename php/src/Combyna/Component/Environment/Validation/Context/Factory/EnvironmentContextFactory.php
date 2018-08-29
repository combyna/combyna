<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Validation\Context\Factory;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Validation\Context\EnvironmentSubValidationContextInterface;
use Combyna\Component\Environment\Validation\Context\Specifier\EnvironmentContextSpecifier;
use Combyna\Component\Environment\Validation\EnvironmentValidationFactoryInterface;
use Combyna\Component\Validator\Context\Factory\SubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class EnvironmentContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentContextFactory implements SubValidationContextFactoryInterface
{
    /**
     * @var EnvironmentValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param EnvironmentValidationFactoryInterface $validationFactory
     */
    public function __construct(EnvironmentValidationFactoryInterface $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }

    /**
     * Creates a EnvironmentSubValidationContext
     *
     * @param EnvironmentContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param EnvironmentNode $environmentNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return EnvironmentSubValidationContextInterface
     */
    public function createEnvironmentContext(
        EnvironmentContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        EnvironmentNode $environmentNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
        return $this->validationFactory->createEnvironmentContext($parentContext, $environmentNode, $behaviourSpec);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            EnvironmentContextSpecifier::class => [$this, 'createEnvironmentContext']
        ];
    }
}
