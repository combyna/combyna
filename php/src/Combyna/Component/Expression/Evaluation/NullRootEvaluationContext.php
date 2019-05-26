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
use Combyna\Component\Expression\Exception\InvalidEvaluationContextException;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Type\TypeInterface;
use LogicException;

/**
 * Class NullRootEvaluationContext
 *
 * Used when evaluating an expression in an isolated "null" context,
 * eg. when evaluating an expression to a static during validation
 *     in order to check for ValuedType value matching.
 *
 * NB: InvalidEvaluationContextException should never be thrown, as validation
 *     should have detected any "impure" expression terms (eg. calling a function,
 *     fetching a widget value) before attempting to evaluate an expression in null context.
 *     See:
 *         - RootValidationContext::validateActNodeInIsolation(...)
 *         - RootValidationContext::wrapInValuedTypeIfPure(...)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NullRootEvaluationContext implements EvaluationContextInterface
{
    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     */
    public function __construct(EvaluationContextFactoryInterface $evaluationContextFactory)
    {
        $this->evaluationContextFactory = $evaluationContextFactory;
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
        throw new InvalidEvaluationContextException(
            sprintf(
                'Unable to call function "%s" of library "%s" from null root evaluation context',
                $functionName,
                $libraryName
            )
        );
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
        throw new LogicException('No assured static is defined with name "' . $assuredStaticName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureLeafwise($captureName)
    {
        throw new LogicException('Null root evaluation context cannot set capture "' . $captureName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureRootwise($captureName)
    {
        throw new LogicException('No capture is defined with name "' . $captureName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment()
    {
        throw new InvalidEvaluationContextException(
            'Unable to fetch the environment from null root evaluation context'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getEventPayloadStatic($staticName)
    {
        throw new InvalidEvaluationContextException(
            sprintf(
                'Unable to fetch event payload static "%s" from null root evaluation context',
                $staticName
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSignalPayloadStatic($staticName)
    {
        throw new InvalidEvaluationContextException(
            sprintf(
                'Unable to fetch signal payload static "%s" from null root evaluation context',
                $staticName
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreSlotStatic($name)
    {
        throw new InvalidEvaluationContextException(
            sprintf(
                'Unable to fetch store slot static "%s" from null root evaluation context',
                $name
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getVariable($variableName)
    {
        throw new LogicException('No variable is defined with name "' . $variableName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetAttribute($attributeName)
    {
        throw new InvalidEvaluationContextException(
            sprintf(
                'Unable to fetch widget attribute "%s" from null root evaluation context',
                $attributeName
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetValue($valueName)
    {
        throw new InvalidEvaluationContextException(
            sprintf(
                'Unable to fetch widget value "%s" from null root evaluation context',
                $valueName
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function makeViewStoreQuery($queryName, StaticBagInterface $argumentStaticBag)
    {
        throw new InvalidEvaluationContextException(
            sprintf(
                'Unable to make query "%s" from null root evaluation context',
                $queryName
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function translate($translationKey, array $parameters = [])
    {
        throw new InvalidEvaluationContextException(
            sprintf(
                'Unable to fetch translation "%s" from null evaluation context',
                $translationKey
            )
        );
    }
}
