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
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use LogicException;

/**
 * Class WidgetGroup
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetGroup implements WidgetGroupInterface
{
    const DEFINITION = 'widget-group';

    /**
     * @var ExpressionBagInterface
     */
    private $captureExpressionBag;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $captureStaticBagModel;

    /**
     * @var WidgetInterface[]
     */
    private $childWidgets = [];

    /**
     * @var string|int
     */
    private $name;

    /**
     * @var WidgetInterface|null
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
     * @var ExpressionInterface|null
     */
    private $visibilityExpression;

    /**
     * @param UiStateFactoryInterface $uiStateFactory
     * @param string|int $name
     * @param FixedStaticBagModelInterface $captureStaticBagModel
     * @param ExpressionBagInterface $captureExpressionBag
     * @param WidgetInterface|null $parentWidget
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     */
    public function __construct(
        UiStateFactoryInterface $uiStateFactory,
        $name,
        FixedStaticBagModelInterface $captureStaticBagModel,
        ExpressionBagInterface $captureExpressionBag,
        WidgetInterface $parentWidget = null,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
        $this->captureExpressionBag = $captureExpressionBag;
        $this->captureStaticBagModel = $captureStaticBagModel;
        $this->name = $name;
        $this->parentWidget = $parentWidget;
        $this->tags = $tags;
        // TODO: Pass this into ->createInitialState(), assuming that's the only place it's ever used??
        $this->uiStateFactory = $uiStateFactory;
        $this->visibilityExpression = $visibilityExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function addChildWidget(WidgetInterface $childWidget)
    {
        $this->childWidgets[$childWidget->getName()] = $childWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function createEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        WidgetStateInterface $widgetState = null
    ) {
        if ($widgetState && !$widgetState instanceof WidgetGroupStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected a %s, got %s',
                    WidgetGroupStateInterface::class,
                    get_class($widgetState)
                )
            );
        }

        return $evaluationContextFactory->createWidgetGroupEvaluationContext(
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
        // Make any capture-definitions (that this widget group defines) and any capture-sets
        // that this widget or any child widget makes available to its other child widgets
        $subEvaluationContext = $this->createEvaluationContext($evaluationContext, $evaluationContextFactory);

        $childWidgetStates = [];

        foreach ($this->childWidgets as $childIndex => $childWidget) {
            $childWidgetStates[$childIndex] = $childWidget->createInitialState(
                $childIndex,
                $subEvaluationContext,
                $evaluationContextFactory
            );
        }

        return $this->uiStateFactory->createWidgetGroupState(
            $name,
            $this,
            $childWidgetStates
        );
    }

    /**
     * {@inheritdoc}
     */
    public function descendantsSetCaptureInclusive($captureName)
    {
        if ($this->captureExpressionBag->hasExpression($captureName)) {
            // This widget sets the capture
            return true;
        }

        foreach ($this->childWidgets as $childWidget) {
            if ($childWidget->descendantsSetCaptureInclusive($captureName)) {
                return true;
            }
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
     * {@inheritdoc}
     */
    public function getAttribute($attributeName, ViewEvaluationContextInterface $evaluationContext)
    {
        throw new LogicException(sprintf(
            'WidgetGroups cannot have attributes, so attribute "%s" cannot be fetched',
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
    public function getChildWidget($childIndex)
    {
        if (!array_key_exists($childIndex, $this->childWidgets)) {
            throw new LogicException(
                sprintf(
                    'Widget group has no child #%d',
                    $childIndex
                )
            );
        }

        return $this->childWidgets[$childIndex];
    }

    /**
     * {@inheritdoc}
     */
    public function getChildWidgets()
    {
        return $this->childWidgets;
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
        $child = $this->childWidgets[$childName];

        if (count($names) === 0) {
            return $child;
        }

        return $child->getDescendantByPath($names);
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
        return true; // Widget groups have a renderer
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateState(
        WidgetStateInterface $oldState,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        if (!$oldState instanceof WidgetGroupStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    WidgetGroupStateInterface::class,
                    get_class($oldState)
                )
            );
        }

        // Make any capture-definitions (that this widget group defines) and any capture-sets
        // that this widget or any child widget makes available to its other child widgets
        $subEvaluationContext = $this->createEvaluationContext(
            $evaluationContext,
            $evaluationContextFactory,
            $oldState
        );

        $childWidgetStates = [];

        foreach ($this->childWidgets as $childIndex => $childWidget) {
            $childWidgetStates[$childIndex] = $childWidget->reevaluateState(
                $oldState->getChildState($childIndex),
                $subEvaluationContext,
                $evaluationContextFactory
            );
        }

        return $oldState->with($childWidgetStates);
    }
}
