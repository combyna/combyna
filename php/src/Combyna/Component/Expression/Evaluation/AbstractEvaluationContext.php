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
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Class AbstractEvaluationContext
 *
 * A delegating abstract evaluation context that can be extended to easily add functionality
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractEvaluationContext implements EvaluationContextInterface
{
    /**
     * @var EvaluationContextFactoryInterface
     */
    protected $evaluationContextFactory;

    /**
     * @var EvaluationContextInterface
     */
    protected $parentContext;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext
    ) {
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->parentContext = $parentContext;
    }

    /**
     * {@inheritdoc}
     */
    public function callFunction(
        $libraryName,
        $functionName,
        StaticBagInterface $argumentStaticBag,
        TypeInterface $returnType
    ) {
        return $this->parentContext->callFunction($libraryName, $functionName, $argumentStaticBag, $returnType);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubAssuredContext(StaticBagInterface $assuredStaticBag)
    {
        return $this->evaluationContextFactory->createAssuredContext($this, $assuredStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubEventEvaluationContext(EventInterface $event)
    {
        return $this->evaluationContextFactory->createEventContext($this, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubExpressionContext(ExpressionInterface $expression)
    {
        return $this->evaluationContextFactory->createExpressionContext($this, $expression);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubScopeContext(StaticBagInterface $variableStaticBag)
    {
        return $this->evaluationContextFactory->createScopeContext($this, $variableStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubSignalEvaluationContext(SignalInterface $signal)
    {
        return $this->evaluationContextFactory->createSignalContext($this, $signal);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStatic($assuredStaticName)
    {
        return $this->parentContext->getAssuredStatic($assuredStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment()
    {
        return $this->parentContext->getEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function getEventPayloadStatic($staticName)
    {
        return $this->parentContext->getEventPayloadStatic($staticName);
    }

    /**
     * {@inheritdoc}
     */
    public function getSignalPayloadStatic($staticName)
    {
        return $this->parentContext->getSignalPayloadStatic($staticName);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreSlotStatic($name)
    {
        return $this->parentContext->getStoreSlotStatic($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getVariable($variableName)
    {
        return $this->parentContext->getVariable($variableName);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetAttribute($attributeName)
    {
        return $this->parentContext->getWidgetAttribute($attributeName);
    }

    /**
     * {@inheritdoc}
     */
    public function makeViewStoreQuery($queryName, StaticBagInterface $argumentStaticBag)
    {
        return $this->parentContext->makeViewStoreQuery($queryName, $argumentStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function translate($translationKey, array $arguments = [])
    {
        return $this->parentContext->translate($translationKey, $arguments);
    }
}
