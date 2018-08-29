<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context\Factory;

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Common\DelegatorInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use InvalidArgumentException;

/**
 * Class DelegatingSubValidationContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingSubValidationContextFactory implements DelegatingSubValidationContextFactoryInterface, DelegatorInterface
{
    /**
     * @var callable[]
     */
    private $contextFactoryCallablesByClass = [];

    /**
     * {@inheritdoc}
     */
    public function addFactory(SubValidationContextFactoryInterface $contextFactory)
    {
        foreach (
            $contextFactory->getSpecifierClassToContextFactoryCallableMap() as
            $specifierClass => $factoryCallable
        ) {
            $this->contextFactoryCallablesByClass[$specifierClass] = $factoryCallable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createContext(
        SubValidationContextSpecifierInterface $contextSpecifier,
        SubValidationContextInterface $parentContext,
        StructuredNodeInterface $structuredNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
        $contextSpecifierClass = get_class($contextSpecifier);

        if (!array_key_exists($contextSpecifierClass, $this->contextFactoryCallablesByClass)) {
            throw new InvalidArgumentException(sprintf(
                'No sub-validation context factory is registered for specifier "%s"',
                $contextSpecifierClass
            ));
        }

        $contextFactoryCallable = $this->contextFactoryCallablesByClass[$contextSpecifierClass];

        return $contextFactoryCallable(
            $contextSpecifier,
            $parentContext,
            $structuredNode,
            $behaviourSpec
        );
    }
}
