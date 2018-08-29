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
use Combyna\Component\Ui\Config\Act\CompoundWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\ViewNodeInterface;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreNode;
use Combyna\Component\Ui\Store\Validation\Context\ViewStoreSubValidationContextInterface;
use Combyna\Component\Ui\Validation\Context\CompoundWidgetDefinitionSubValidationContextInterface;
use Combyna\Component\Ui\Validation\Context\ViewSubValidationContextInterface;
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
     * @return CompoundWidgetDefinitionSubValidationContextInterface
     */
    public function createCompoundWidgetDefinitionContext(
        SubValidationContextInterface $parentContext,
        CompoundWidgetDefinitionNode $definitionNode,
        BehaviourSpecInterface $definitionNodeBehaviourSpec
    );

    /**
     * Creates a ViewSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param ViewNodeInterface $viewNode
     * @param BehaviourSpecInterface $viewNodeBehaviourSpec
     * @return ViewSubValidationContextInterface
     */
    public function createViewContext(
        SubValidationContextInterface $parentContext,
        ViewNodeInterface $viewNode,
        BehaviourSpecInterface $viewNodeBehaviourSpec
    );

    /**
     * Creates a ViewStoreSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param ViewStoreNode $viewStoreNode
     * @param BehaviourSpecInterface $viewStoreNodeBehaviourSpec
     * @return ViewStoreSubValidationContextInterface
     */
    public function createViewStoreContext(
        SubValidationContextInterface $parentContext,
        ViewStoreNode $viewStoreNode,
        BehaviourSpecInterface $viewStoreNodeBehaviourSpec
    );
}
