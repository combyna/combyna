<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Context\Factory;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Ui\Config\Act\CompoundWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\ViewNodeInterface;
use Combyna\Component\Ui\Validation\Context\CompoundWidgetDefinitionSubValidationContextInterface;
use Combyna\Component\Ui\Validation\Context\Specifier\CompoundWidgetContextSpecifier;
use Combyna\Component\Ui\Validation\Context\Specifier\ViewContextSpecifier;
use Combyna\Component\Ui\Validation\Context\ViewSubValidationContextInterface;
use Combyna\Component\Ui\Validation\UiValidationFactoryInterface;
use Combyna\Component\Validator\Context\Factory\SubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class UiContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiContextFactory implements SubValidationContextFactoryInterface
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
     * Creates a CompoundWidgetDefinitionSubValidationContext
     *
     * @param CompoundWidgetContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param CompoundWidgetDefinitionNode $compoundWidgetDefinitionNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return CompoundWidgetDefinitionSubValidationContextInterface
     */
    public function createCompoundWidgetContext(
        CompoundWidgetContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        CompoundWidgetDefinitionNode $compoundWidgetDefinitionNode,
        BehaviourSpecInterface $behaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return $this->validationFactory->createCompoundWidgetDefinitionContext(
            $parentContext,
            $compoundWidgetDefinitionNode,
            $behaviourSpec,
            $subjectNode
        );
    }

    /**
     * Creates a ViewSubValidationContext
     *
     * @param ViewContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param ViewNodeInterface $viewNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return ViewSubValidationContextInterface
     */
    public function createViewContext(
        ViewContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        ViewNodeInterface $viewNode,
        BehaviourSpecInterface $behaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return $this->validationFactory->createViewContext($parentContext, $viewNode, $behaviourSpec, $subjectNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            CompoundWidgetContextSpecifier::class => [$this, 'createCompoundWidgetContext'],
            ViewContextSpecifier::class => [$this, 'createViewContext']
        ];
    }
}
