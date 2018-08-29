<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Validation\Context\Factory;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreNode;
use Combyna\Component\Ui\Store\Validation\Context\Specifier\ViewStoreContextSpecifier;
use Combyna\Component\Ui\Store\Validation\Context\ViewStoreSubValidationContextInterface;
use Combyna\Component\Ui\Validation\UiValidationFactoryInterface;
use Combyna\Component\Validator\Context\Factory\SubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class ViewStoreContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreContextFactory implements SubValidationContextFactoryInterface
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
     * Creates a ViewStoreSubValidationContext
     *
     * @param ViewStoreContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param ViewStoreNode $viewStoreNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return ViewStoreSubValidationContextInterface
     */
    public function createViewStoreContext(
        ViewStoreContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        ViewStoreNode $viewStoreNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
        return $this->validationFactory->createViewStoreContext($parentContext, $viewStoreNode, $behaviourSpec);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            ViewStoreContextSpecifier::class => [$this, 'createViewStoreContext']
        ];
    }
}
