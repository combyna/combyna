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
use Combyna\Component\Expression\Config\Act\GuardExpressionNode;
use Combyna\Component\Expression\Validation\Context\AssuredSubValidationContextInterface;
use Combyna\Component\Expression\Validation\Context\Specifier\AssuredContextSpecifier;
use Combyna\Component\Validator\Context\Factory\SubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;

/**
 * Class AssuredContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredContextFactory implements SubValidationContextFactoryInterface
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
     * Creates an AssuredSubValidationContext
     *
     * @param AssuredContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param GuardExpressionNode $guardExpressionNode
     * @param BehaviourSpecInterface $guardExpressionNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return AssuredSubValidationContextInterface
     */
    public function createAssuredContext(
        AssuredContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        GuardExpressionNode $guardExpressionNode,
        BehaviourSpecInterface $guardExpressionNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return $this->validationFactory->createAssuredContext(
            $parentContext,
            $guardExpressionNode,
            $guardExpressionNodeBehaviourSpec,
            $guardExpressionNode->getAssuranceNodes(),
            $subjectNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            AssuredContextSpecifier::class => [$this, 'createAssuredContext']
        ];
    }
}
