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
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Expression\StaticListExpression;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\Widget\RepeaterWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use LogicException;

/**
 * Class RepeaterWidget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RepeaterWidget implements RepeaterWidgetInterface
{
    const DEFINITION = 'repeater';
    const REPEATED_WIDGET_NAME = 'repeated';

    /**
     * @var ExpressionBagInterface
     */
    private $captureExpressionBag;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $captureStaticBagModel;

    /**
     * @var string|null
     */
    private $indexVariableName;

    /**
     * @var ExpressionInterface
     */
    private $itemListExpression;

    /**
     * @var string
     */
    private $itemVariableName;

    /**
     * @var string|int
     */
    private $name;

    /**
     * @var WidgetInterface
     */
    private $parentWidget;

    /**
     * @var WidgetInterface|null
     */
    private $repeatedWidget;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var ExpressionInterface|null
     */
    private $visibilityExpression;

    /**
     * @param WidgetInterface $parentWidget
     * @param string|int $name
     * @param ExpressionInterface $itemListExpression
     * @param string|null $indexVariableName
     * @param string $itemVariableName
     * @param UiStateFactoryInterface $uiStateFactory
     * @param FixedStaticBagModelInterface $captureStaticBagModel
     * @param ExpressionBagInterface $captureExpressionBag
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     */
    public function __construct(
        WidgetInterface $parentWidget,
        $name,
        ExpressionInterface $itemListExpression,
        $indexVariableName,
        $itemVariableName,
        UiStateFactoryInterface $uiStateFactory,
        FixedStaticBagModelInterface $captureStaticBagModel,
        ExpressionBagInterface $captureExpressionBag,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
        $this->captureExpressionBag = $captureExpressionBag;
        $this->captureStaticBagModel = $captureStaticBagModel;
        $this->indexVariableName = $indexVariableName;
        $this->itemListExpression = $itemListExpression;
        $this->itemVariableName = $itemVariableName;
        $this->name = $name;
        $this->parentWidget = $parentWidget;
        $this->tags = $tags;
        $this->uiStateFactory = $uiStateFactory;
        $this->visibilityExpression = $visibilityExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function createEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        WidgetStateInterface $widgetState = null
    ) {
        if ($widgetState && !$widgetState instanceof RepeaterWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected a %s, got %s',
                    RepeaterWidgetStateInterface::class,
                    get_class($widgetState)
                )
            );
        }

        return $evaluationContextFactory->createRepeaterWidgetEvaluationContext(
            $parentContext,
            $this,
            $widgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag)
    {
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
        if ($this->repeatedWidget === null) {
            // This should never happen, but just in case
            throw new LogicException('Repeated widget has not been set for repeater');
        }

        // Make any capture-definitions (that this repeater defines) and any capture-sets
        // that this widget or any child widget makes available to its other child widgets
        $subEvaluationContext = $this->createEvaluationContext($evaluationContext, $evaluationContextFactory);

        // Create one instance of the repeated widget's state for each item in the list
        $repeatedWidgetStates = $this->mapItemStaticList(
            function (
                ViewEvaluationContextInterface $itemEvaluationContext,
                StaticInterface $static,
                $index
            ) use ($evaluationContextFactory) {
                // Use the index of each repeated instance as its name
                return $this->repeatedWidget->createInitialState(
                    $index,
                    $itemEvaluationContext,
                    $evaluationContextFactory
                );
            },
            $subEvaluationContext
        );

        return $this->uiStateFactory->createRepeaterWidgetState(
            $name,
            $this,
            $repeatedWidgetStates
        );
    }

    /**
     * {@inheritdoc}
     */
    public function descendantsSetCaptureInclusive($captureName)
    {
        return $this->captureExpressionBag->hasExpression($captureName) ||
            $this->repeatedWidget->descendantsSetCaptureInclusive($captureName);
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
        if ($this->parentWidget === null) {
            // No parent widget - nothing to do, as there is nothing to handle the event
            return $programState;
        }

        return $this->parentWidget->dispatchEvent(
            $programState,
            $program,
            $event,
            $widgetEvaluationContext
        );
    }

    /**
     * Evaluates the item list expression to a static list
     *
     * @param WidgetEvaluationContextInterface $evaluationContext
     * @return StaticListExpression
     */
    private function evaluateItemStaticList(WidgetEvaluationContextInterface $evaluationContext)
    {
        $itemStaticList = $this->itemListExpression->toStatic($evaluationContext);

        if (!$itemStaticList instanceof StaticListExpression) {
            // This should never happen as it should be caught by validation, but just in case
            throw new LogicException(
                sprintf(
                    'Repeated widget item list should have evaluated to a "%s", but it was a "%s"',
                    StaticListExpression::TYPE,
                    $itemStaticList->getType()
                )
            );
        }

        return $itemStaticList;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attributeName, ViewEvaluationContextInterface $evaluationContext)
    {
        throw new LogicException(sprintf(
            'RepeaterWidgets cannot have attributes, so attribute "%s" cannot be fetched',
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

        if ($this->repeatedWidget === null) {
            // This should never happen, but just in case
            throw new LogicException('Repeated widget has not been set for repeater');
        }

        if ($childName !== self::REPEATED_WIDGET_NAME) {
            throw new LogicException(
                sprintf(
                    'Repeater widget only supports a single child called "%s" but "%s" was requested',
                    self::REPEATED_WIDGET_NAME,
                    $childName
                )
            );
        }

        if (count($names) === 0) {
            return $this->repeatedWidget;
        }

        return $this->repeatedWidget->getDescendantByPath($names);
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
    public function getRepeatedWidget()
    {
        return $this->repeatedWidget;
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
        return true; // Repeaters have a renderer
    }

    /**
     * {@inheritdoc}
     */
    public function mapItemStaticList(callable $mapCallback, WidgetEvaluationContextInterface $evaluationContext)
    {
        $itemStaticList = $this->evaluateItemStaticList($evaluationContext);

        return $itemStaticList->mapArray(
            $this->itemVariableName,
            $this->indexVariableName,
            $mapCallback,
            $evaluationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateState(
        WidgetStateInterface $oldState,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        if ($this->repeatedWidget === null) {
            // This should never happen, but just in case
            throw new LogicException('Repeated widget has not been set for repeater');
        }

        if (!$oldState instanceof RepeaterWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    RepeaterWidgetStateInterface::class,
                    get_class($oldState)
                )
            );
        }

        // Make any capture-definitions (that this repeater defines) and any capture-sets
        // that this widget or any child widget makes available to its other child widgets
        $subEvaluationContext = $this->createEvaluationContext(
            $evaluationContext,
            $evaluationContextFactory,
            $oldState
        );

        // Create one instance of the repeated widget's state for each item in the list
        $repeatedWidgetStates = $this->mapItemStaticList(
            function (
                ViewEvaluationContextInterface $itemEvaluationContext,
                StaticInterface $static,
                $index
            ) use ($evaluationContextFactory, $oldState) {
                if (!$oldState->hasChildState($index)) {
                    // This repeated item did not exist before (ie. the number of items repeated
                    // has changed) - so we need to create a new initial state for the newly added item(s)
                    return $this->repeatedWidget->createInitialState(
                        $index,
                        $itemEvaluationContext,
                        $evaluationContextFactory
                    );
                }

                // Use the index of each repeated instance as its name
                return $this->repeatedWidget->reevaluateState(
                    $oldState->getChildState($index),
                    $itemEvaluationContext,
                    $evaluationContextFactory
                );
            },
            $subEvaluationContext
        );

        return $oldState->with($repeatedWidgetStates);
    }

    /**
     * {@inheritdoc}
     */
    public function setRepeatedWidget(WidgetInterface $repeatedWidget)
    {
        $this->repeatedWidget = $repeatedWidget;
    }
}
