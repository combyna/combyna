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
use Combyna\Component\Ui\Store\Validation\Context\ViewStoreSubValidationContextInterface;
use Combyna\Component\Ui\Validation\Context\CompoundWidgetDefinitionSubValidationContextInterface;
use Combyna\Component\Ui\Validation\Context\ConditionalWidgetSubValidationContextInterface;
use Combyna\Component\Ui\Validation\Context\DefinedWidgetSubValidationContextInterface;
use Combyna\Component\Ui\Validation\Context\PrimitiveWidgetDefinitionSubValidationContextInterface;
use Combyna\Component\Ui\Validation\Context\RepeaterWidgetSubValidationContextInterface;
use Combyna\Component\Ui\Validation\Context\ViewSubValidationContextInterface;
use Combyna\Component\Ui\Validation\Context\WidgetGroupSubValidationContextInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Interface UiValidationFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiValidationFactoryInterface
{
    /**
     * Creates a CompoundWidgetDefinitionSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param CompoundWidgetDefinitionNode $definitionNode
     * @param BehaviourSpecInterface $definitionNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return CompoundWidgetDefinitionSubValidationContextInterface
     */
    public function createCompoundWidgetDefinitionContext(
        SubValidationContextInterface $parentContext,
        CompoundWidgetDefinitionNode $definitionNode,
        BehaviourSpecInterface $definitionNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    );

    /**
     * Creates a ConditionalWidgetSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param ConditionalWidgetNode $conditionalWidgetNode
     * @param BehaviourSpecInterface $conditionalWidgetNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return ConditionalWidgetSubValidationContextInterface
     */
    public function createConditionalWidgetContext(
        SubValidationContextInterface $parentContext,
        ConditionalWidgetNode $conditionalWidgetNode,
        BehaviourSpecInterface $conditionalWidgetNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    );

    /**
     * Creates a DefinedWidgetSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param DefinedWidgetNode $widgetNode
     * @param BehaviourSpecInterface $widgetNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return DefinedWidgetSubValidationContextInterface
     */
    public function createDefinedWidgetContext(
        SubValidationContextInterface $parentContext,
        DefinedWidgetNode $widgetNode,
        BehaviourSpecInterface $widgetNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    );

    /**
     * Creates a PrimitiveWidgetDefinitionSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param PrimitiveWidgetDefinitionNode $definitionNode
     * @param BehaviourSpecInterface $definitionNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return PrimitiveWidgetDefinitionSubValidationContextInterface
     */
    public function createPrimitiveWidgetDefinitionContext(
        SubValidationContextInterface $parentContext,
        PrimitiveWidgetDefinitionNode $definitionNode,
        BehaviourSpecInterface $definitionNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    );

    /**
     * Creates a RepeaterWidgetSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param RepeaterWidgetNode $repeaterWidgetNode
     * @param BehaviourSpecInterface $repeaterWidgetNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return RepeaterWidgetSubValidationContextInterface
     */
    public function createRepeaterWidgetContext(
        SubValidationContextInterface $parentContext,
        RepeaterWidgetNode $repeaterWidgetNode,
        BehaviourSpecInterface $repeaterWidgetNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    );

    /**
     * Creates a ViewSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param ViewNodeInterface $viewNode
     * @param BehaviourSpecInterface $viewNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return ViewSubValidationContextInterface
     */
    public function createViewContext(
        SubValidationContextInterface $parentContext,
        ViewNodeInterface $viewNode,
        BehaviourSpecInterface $viewNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    );

    /**
     * Creates a ViewStoreSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param ViewStoreNode $viewStoreNode
     * @param BehaviourSpecInterface $viewStoreNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return ViewStoreSubValidationContextInterface
     */
    public function createViewStoreContext(
        SubValidationContextInterface $parentContext,
        ViewStoreNode $viewStoreNode,
        BehaviourSpecInterface $viewStoreNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    );

    /**
     * Creates a WidgetGroupSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param WidgetGroupNode $widgetGroupNode
     * @param BehaviourSpecInterface $widgetGroupNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return WidgetGroupSubValidationContextInterface
     */
    public function createWidgetGroupContext(
        SubValidationContextInterface $parentContext,
        WidgetGroupNode $widgetGroupNode,
        BehaviourSpecInterface $widgetGroupNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    );
}
