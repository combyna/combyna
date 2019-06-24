<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Widget;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\Widget\ConditionalWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use LogicException;

/**
 * Class ConditionalWidget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalWidget implements ConditionalWidgetInterface
{
    const ALTERNATE_WIDGET_NAME = 'alternate';
    const CONSEQUENT_WIDGET_NAME = 'consequent';
    const DEFINITION = 'conditional';

    /**
     * @var WidgetInterface|null
     */
    private $alternateWidget = null;

    /**
     * @var ExpressionBagInterface
     */
    private $captureExpressionBag;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $captureStaticBagModel;

    /**
     * @var ExpressionInterface
     */
    private $conditionExpression;

    /**
     * @var WidgetInterface|null
     */
    private $consequentWidget = null;

    /**
     * @var string|int
     */
    private $name;

    /**
     * @var WidgetInterface
     */
    private $parentWidget;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @param WidgetInterface $parentWidget
     * @param ExpressionInterface $conditionExpression
     * @param string|int $name
     * @param UiStateFactoryInterface $uiStateFactory
     * @param FixedStaticBagModelInterface $captureStaticBagModel
     * @param ExpressionBagInterface $captureExpressionBag
     * @param array $tags
     */
    public function __construct(
        WidgetInterface $parentWidget,
        ExpressionInterface $conditionExpression,
        $name,
        UiStateFactoryInterface $uiStateFactory,
        FixedStaticBagModelInterface $captureStaticBagModel,
        ExpressionBagInterface $captureExpressionBag,
        array $tags = []
    ) {
        $this->captureExpressionBag = $captureExpressionBag;
        $this->captureStaticBagModel = $captureStaticBagModel;
        $this->conditionExpression = $conditionExpression;
        $this->name = $name;
        $this->parentWidget = $parentWidget;
        $this->tags = $tags;
        $this->uiStateFactory = $uiStateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        WidgetStateInterface $widgetState = null
    ) {
        if ($widgetState && !$widgetState instanceof ConditionalWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected a %s, got %s',
                    ConditionalWidgetStateInterface::class,
                    get_class($widgetState)
                )
            );
        }

        return $evaluationContextFactory->createConditionalWidgetEvaluationContext(
            $parentContext,
            $this,
            $widgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent(
        $libraryName,
        $eventName,
        array $payloadNatives,
        ViewEvaluationContextInterface $evaluationContext
    ) {
        throw new \Exception('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(
        $name,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        if ($this->consequentWidget === null) {
            // This should never happen, but just in case
            throw new LogicException('Consequent widget has not been set for conditional');
        }

        // Make any capture-definitions (that this conditional widget defines) and any capture-sets
        // that this widget or any child widget makes available to its other child widgets
        $subEvaluationContext = $this->createEvaluationContext($evaluationContext, $evaluationContextFactory);

        // Evaluate the condition to determine whether the consequent widget is present
        $conditionStatic = $this->conditionExpression->toStatic($subEvaluationContext);

        $consequentWidgetState = null;
        $alternateWidgetState = null;

        if ($conditionStatic->toNative() === true) {
            // Condition evaluated to true, so the consequent widget is present (and any alternate widget is not)
            $consequentWidgetState = $this->consequentWidget->createInitialState(
                self::CONSEQUENT_WIDGET_NAME,
                $subEvaluationContext,
                $evaluationContextFactory
            );
        } elseif ($this->alternateWidget !== null) {
            // Condition evaluated to false, so if the alternate widget is defined it is present
            // (and the consequent widget is not). If the alternate widget is not defined,
            // nothing is present
            $alternateWidgetState = $this->alternateWidget->createInitialState(
                self::ALTERNATE_WIDGET_NAME,
                $subEvaluationContext,
                $evaluationContextFactory
            );
        }

        return $this->uiStateFactory->createConditionalWidgetState(
            $name,
            $this,
            $consequentWidgetState,
            $alternateWidgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function descendantsSetCaptureInclusive($captureName)
    {
        if ($this->consequentWidget === null) {
            // This should never happen, but just in case
            throw new LogicException('Consequent widget has not been set for conditional');
        }

        if ($this->captureExpressionBag->hasExpression($captureName)) {
            return true;
        }

        if ($this->consequentWidget->descendantsSetCaptureInclusive($captureName)) {
            return true;
        }

        if ($this->alternateWidget &&
            $this->alternateWidget->descendantsSetCaptureInclusive($captureName)
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchEvent(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        EventInterface $event,
        WidgetEvaluationContextInterface $widgetEvaluationContext
    ) {
        // Conditional widgets cannot define any triggers, so bubble the event
        // up to the parent widget to handle
        return $this->parentWidget->dispatchEvent(
            $programState,
            $program,
            $event,
            $widgetEvaluationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAlternateWidget()
    {
        return $this->alternateWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attributeName, ViewEvaluationContextInterface $evaluationContext)
    {
        throw new LogicException(sprintf(
            'ConditionalWidgets cannot have attributes, so attribute "%s" cannot be fetched',
            $attributeName
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureExpressionBag()
    {
        return $this->captureExpressionBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureStaticBagModel()
    {
        return $this->captureStaticBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        return $this->conditionExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function getConsequentWidget()
    {
        return $this->consequentWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionLibraryName()
    {
        return self::LIBRARY;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionName()
    {
        return self::DEFINITION;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescendantByPath(array $names)
    {
        $childName = array_shift($names);

        if ($this->consequentWidget === null) {
            // This should never happen, but just in case
            throw new LogicException('Consequent widget has not been set for conditional');
        }

        if ($childName !== self::CONSEQUENT_WIDGET_NAME && $childName !== self::ALTERNATE_WIDGET_NAME) {
            throw new LogicException(
                sprintf(
                    'Conditional widget only supports children called "%s" and "%s" but "%s" was requested',
                    self::CONSEQUENT_WIDGET_NAME,
                    self::ALTERNATE_WIDGET_NAME,
                    $childName
                )
            );
        }

        if ($childName === self::CONSEQUENT_WIDGET_NAME) {
            $childWidget = $this->consequentWidget;
        } else {
            if ($this->alternateWidget === null) {
                throw new LogicException('Conditional does not define an alternate widget');
            }

            $childWidget = $this->alternateWidget;
        }

        return count($names) === 0 ?
            $childWidget :
            $childWidget->getDescendantByPath($names);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentWidget()
    {
        return $this->parentWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $prefix = ($this->parentWidget !== null) ?
            $this->parentWidget->getPath() :
            [];

        return array_merge($prefix, [$this->name]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTag($tag)
    {
        return array_key_exists($tag, $this->tags) && $this->tags[$tag] === true;
    }

    /**
     * {@inheritdoc}
     */
    public function isRenderable()
    {
        return true; // ConditionalWidgets have a renderer
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateState(
        WidgetStateInterface $oldState,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        if ($this->consequentWidget === null) {
            // This should never happen, but just in case
            throw new LogicException('Consequent widget has not been set for conditional');
        }

        if (!$oldState instanceof ConditionalWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    ConditionalWidgetStateInterface::class,
                    get_class($oldState)
                )
            );
        }

        // Make any capture-definitions (that this conditional widget defines) and any capture-sets
        // that this widget or any child widget makes available to its other child widgets
        $subEvaluationContext = $this->createEvaluationContext($evaluationContext, $evaluationContextFactory);

        // Evaluate the condition to determine whether the consequent widget is present
        $conditionStatic = $this->conditionExpression->toStatic($subEvaluationContext);

        $consequentWidgetState = null;
        $alternateWidgetState = null;

        if ($conditionStatic->toNative() === true) {
            // Condition evaluated to true, so the consequent widget is present (and any alternate widget is not)
            $consequentWidgetState = $oldState->getConsequentWidgetState() !== null ?
                $this->consequentWidget->reevaluateState(
                    $oldState->getConsequentWidgetState(),
                    $subEvaluationContext,
                    $evaluationContextFactory
                ) :
                $this->consequentWidget->createInitialState(
                    self::CONSEQUENT_WIDGET_NAME,
                    $subEvaluationContext,
                    $evaluationContextFactory
                );
        } elseif ($this->alternateWidget !== null) {
            // Condition evaluated to false, so if the alternate widget is defined it is present
            // (and the consequent widget is not). If the alternate widget is not defined,
            // nothing is present
            $alternateWidgetState = $oldState->getAlternateWidgetState() !== null ?
                $this->alternateWidget->reevaluateState(
                    $oldState->getAlternateWidgetState(),
                    $subEvaluationContext,
                    $evaluationContextFactory
                ) :
                $this->alternateWidget->createInitialState(
                    self::ALTERNATE_WIDGET_NAME,
                    $subEvaluationContext,
                    $evaluationContextFactory
                );
        }

        return $oldState->with($consequentWidgetState, $alternateWidgetState);
    }

    /**
     * {@inheritdoc}
     */
    public function setAlternateWidget(WidgetInterface $alternateWidget = null)
    {
        $this->alternateWidget = $alternateWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function setConsequentWidget(WidgetInterface $consequentWidget)
    {
        $this->consequentWidget = $consequentWidget;
    }
}
