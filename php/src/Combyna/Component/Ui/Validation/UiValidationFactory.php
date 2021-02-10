<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Ui\Config\Act\CompoundWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\ConditionalWidgetNode;
use Combyna\Component\Ui\Config\Act\DefinedWidgetNode;
use Combyna\Component\Ui\Config\Act\PrimitiveWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\RepeaterWidgetNode;
use Combyna\Component\Ui\Config\Act\ViewNodeInterface;
use Combyna\Component\Ui\Config\Act\WidgetGroupNode;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreNode;
use Combyna\Component\Ui\Store\Validation\Context\ViewStoreSubValidationContext;
use Combyna\Component\Ui\Validation\Context\CompoundWidgetDefinitionSubValidationContext;
use Combyna\Component\Ui\Validation\Context\ConditionalWidgetSubValidationContext;
use Combyna\Component\Ui\Validation\Context\DefinedWidgetSubValidationContext;
use Combyna\Component\Ui\Validation\Context\PrimitiveWidgetDefinitionSubValidationContext;
use Combyna\Component\Ui\Validation\Context\RepeaterWidgetSubValidationContext;
use Combyna\Component\Ui\Validation\Context\ViewSubValidationContext;
use Combyna\Component\Ui\Validation\Context\WidgetGroupSubValidationContext;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class UiValidationFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiValidationFactory implements UiValidationFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createCompoundWidgetDefinitionContext(
        SubValidationContextInterface $parentContext,
        CompoundWidgetDefinitionNode $definitionNode,
        BehaviourSpecInterface $definitionNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new CompoundWidgetDefinitionSubValidationContext(
            $parentContext,
            $definitionNode,
            $definitionNodeBehaviourSpec,
            $subjectNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createConditionalWidgetContext(
        SubValidationContextInterface $parentContext,
        ConditionalWidgetNode $conditionalWidgetNode,
        BehaviourSpecInterface $conditionalWidgetNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new ConditionalWidgetSubValidationContext(
            $parentContext,
            $conditionalWidgetNode,
            $conditionalWidgetNodeBehaviourSpec,
            $subjectNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createDefinedWidgetContext(
        SubValidationContextInterface $parentContext,
        DefinedWidgetNode $widgetNode,
        BehaviourSpecInterface $widgetNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new DefinedWidgetSubValidationContext(
            $parentContext,
            $widgetNode,
            $widgetNodeBehaviourSpec,
            $subjectNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createPrimitiveWidgetDefinitionContext(
        SubValidationContextInterface $parentContext,
        PrimitiveWidgetDefinitionNode $definitionNode,
        BehaviourSpecInterface $definitionNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new PrimitiveWidgetDefinitionSubValidationContext(
            $parentContext,
            $definitionNode,
            $definitionNodeBehaviourSpec,
            $subjectNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createRepeaterWidgetContext(
        SubValidationContextInterface $parentContext,
        RepeaterWidgetNode $repeaterWidgetNode,
        BehaviourSpecInterface $repeaterWidgetNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new RepeaterWidgetSubValidationContext(
            $parentContext,
            $repeaterWidgetNode,
            $repeaterWidgetNodeBehaviourSpec,
            $subjectNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createViewContext(
        SubValidationContextInterface $parentContext,
        ViewNodeInterface $viewNode,
        BehaviourSpecInterface $viewNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new ViewSubValidationContext($parentContext, $viewNode, $viewNodeBehaviourSpec, $subjectNode);
    }

    /**
     * {@inheritdoc}
     */
    public function createViewStoreContext(
        SubValidationContextInterface $parentContext,
        ViewStoreNode $viewStoreNode,
        BehaviourSpecInterface $viewStoreNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new ViewStoreSubValidationContext(
            $parentContext,
            $viewStoreNode,
            $viewStoreNodeBehaviourSpec,
            $subjectNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetGroupContext(
        SubValidationContextInterface $parentContext,
        WidgetGroupNode $widgetGroupNode,
        BehaviourSpecInterface $widgetGroupNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new WidgetGroupSubValidationContext(
            $parentContext,
            $widgetGroupNode,
            $widgetGroupNodeBehaviourSpec,
            $subjectNode
        );
    }
}
