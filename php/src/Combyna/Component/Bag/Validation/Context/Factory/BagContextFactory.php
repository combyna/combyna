<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Validation\Context\Factory;

use Combyna\Component\Bag\Validation\Context\ExpressionBagSubValidationContext;
use Combyna\Component\Bag\Validation\Context\Specifier\ExpressionBagContextSpecifier;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Ui\Validation\UiValidationFactoryInterface;
use Combyna\Component\Validator\Context\Factory\SubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class BagContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BagContextFactory implements SubValidationContextFactoryInterface
{
    /**
     * @var UiValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param UiValidationFactoryInterface $validationFactory
     */
    public function __construct(UiValidationFactoryInterface $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }

    /**
     * Creates an ExpressionBagSubValidationContext
     *
     * @param ExpressionBagContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param ActNodeInterface $containerNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return ExpressionBagSubValidationContext
     */
    public function createExpressionBagContext(
        ExpressionBagContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        ActNodeInterface $containerNode,
        BehaviourSpecInterface $behaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new ExpressionBagSubValidationContext(
            $parentContext,
            $containerNode,
            $behaviourSpec,
            $subjectNode,
            $specifier->getExpressionNodes()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            ExpressionBagContextSpecifier::class => [$this, 'createExpressionBagContext']
        ];
    }
}
