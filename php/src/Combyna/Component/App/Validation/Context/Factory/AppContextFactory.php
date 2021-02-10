<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Validation\Context\Factory;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\App\Validation\AppValidationFactoryInterface;
use Combyna\Component\App\Validation\Context\AppSubValidationContextInterface;
use Combyna\Component\App\Validation\Context\Specifier\AppContextSpecifier;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Validator\Context\Factory\SubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class AppContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppContextFactory implements SubValidationContextFactoryInterface
{
    /**
     * @var AppValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param AppValidationFactoryInterface $validationFactory
     */
    public function __construct(AppValidationFactoryInterface $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }

    /**
     * Creates an AppSubValidationContext
     *
     * @param AppContextSpecifier $specifier
     * @param SubValidationContextInterface $rootSubContext
     * @param AppNode $appNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return AppSubValidationContextInterface
     */
    public function createAppContext(
        AppContextSpecifier $specifier,
        SubValidationContextInterface $rootSubContext,
        AppNode $appNode,
        BehaviourSpecInterface $behaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return $this->validationFactory->createAppContext($rootSubContext, $appNode, $behaviourSpec, $subjectNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            AppContextSpecifier::class => [$this, 'createAppContext']
        ];
    }
}
