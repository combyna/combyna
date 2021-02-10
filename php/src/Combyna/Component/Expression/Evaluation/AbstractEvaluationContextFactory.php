<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Evaluation;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Signal\SignalInterface;

/**
 * Class AbstractEvaluationContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractEvaluationContextFactory implements EvaluationContextFactoryInterface
{
    /**
     * @var EvaluationContextFactoryInterface
     */
    protected $parentContextFactory;

    /**
     * @param EvaluationContextFactoryInterface $parentContextFactory
     */
    public function __construct(
        EvaluationContextFactoryInterface $parentContextFactory
    ) {
        $this->parentContextFactory = $parentContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createAssuredContext(
        EvaluationContextInterface $parentContext,
        StaticBagInterface $assuredStaticBag
    ) {
        return $this->parentContextFactory->createAssuredContext(
            $parentContext,
            $assuredStaticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createEventContext(
        EvaluationContextInterface $parentContext,
        EventInterface $event
    ) {
        return $this->parentContextFactory->createEventContext($parentContext, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function createExpressionContext(
        EvaluationContextInterface $parentContext,
        ExpressionInterface $expression
    ) {
        return $this->parentContextFactory->createExpressionContext($parentContext, $expression);
    }

    /**
     * {@inheritdoc}
     */
    public function createNullRootContext()
    {
        return $this->parentContextFactory->createNullRootContext();
    }

    /**
     * {@inheritdoc}
     */
    public function createRootContext(ResourceRepositoryInterface $resourceRepository)
    {
        return $this->parentContextFactory->createRootContext($resourceRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function createScopeContext(
        EvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag
    ) {
        return $this->parentContextFactory->createScopeContext(
            $parentContext,
            $variableStaticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createSignalContext(
        EvaluationContextInterface $parentContext,
        SignalInterface $signal
    ) {
        return $this->parentContextFactory->createSignalContext(
            $parentContext,
            $signal
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParentStateTypeToContextFactoryMap()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getStateTypeToContextFactoryMap()
    {
        return [];
    }
}
