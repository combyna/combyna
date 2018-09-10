<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Validation\Context\Factory;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Signal\Config\Act\SignalHandlerNode;
use Combyna\Component\Signal\Validation\Context\SignalHandlerSubValidationContextInterface;
use Combyna\Component\Signal\Validation\Context\Specifier\SignalHandlerContextSpecifier;
use Combyna\Component\Signal\Validation\SignalValidationFactoryInterface;
use Combyna\Component\Validator\Context\Factory\SubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class SignalHandlerContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalHandlerContextFactory implements SubValidationContextFactoryInterface
{
    /**
     * @var SignalValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param SignalValidationFactoryInterface $validationFactory
     */
    public function __construct(SignalValidationFactoryInterface $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }

    /**
     * Creates a SignalHandlerSubValidationContext
     *
     * @param SignalHandlerContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param SignalHandlerNode $signalHandlerNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return SignalHandlerSubValidationContextInterface
     */
    public function createSignalHandlerContext(
        SignalHandlerContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        SignalHandlerNode $signalHandlerNode,
        BehaviourSpecInterface $behaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return $this->validationFactory->createSignalHandlerContext(
            $parentContext,
            $signalHandlerNode,
            $behaviourSpec,
            $subjectNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            SignalHandlerContextSpecifier::class => [$this, 'createSignalHandlerContext']
        ];
    }
}
