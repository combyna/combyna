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
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Trigger\TriggerCollectionInterface;
use Combyna\Component\Ui\Evaluation\DefinedWidgetEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use LogicException;

/**
 * Class DefinedWidget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DefinedWidget implements DefinedWidgetInterface
{
    /**
     * @var ExpressionBagInterface
     */
    private $attributeExpressions;

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
     * @var WidgetDefinitionInterface
     */
    private $definition;

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
     * @var TriggerCollectionInterface
     */
    private $triggerCollection;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var ExpressionInterface|null
     */
    private $visibilityExpression;

    /**
     * @param WidgetInterface|null $parentWidget
     * @param string|int $name
     * @param WidgetDefinitionInterface $definition
     * @param ExpressionBagInterface $attributeExpressions
     * @param UiStateFactoryInterface $uiStateFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param TriggerCollectionInterface $triggerCollection
     * @param FixedStaticBagModelInterface $captureStaticBagModel
     * @param ExpressionBagInterface $captureExpressionBag
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     */
    public function __construct(
        WidgetInterface $parentWidget = null,
        $name,
        WidgetDefinitionInterface $definition,
        ExpressionBagInterface $attributeExpressions,
        UiStateFactoryInterface $uiStateFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory,
        TriggerCollectionInterface $triggerCollection,
        FixedStaticBagModelInterface $captureStaticBagModel,
        ExpressionBagInterface $captureExpressionBag,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
        $this->attributeExpressions = $attributeExpressions;
        $this->captureExpressionBag = $captureExpressionBag;
        $this->captureStaticBagModel = $captureStaticBagModel;
        $this->childWidgets = [];
        $this->definition = $definition;
        $this->name = $name;
        $this->parentWidget = $parentWidget;
        $this->tags = $tags;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
        $this->uiStateFactory = $uiStateFactory;
        $this->visibilityExpression = $visibilityExpression;
        $this->triggerCollection = $triggerCollection;
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
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag)
    {
        $this->definition->assertValidAttributeStaticBag($attributeStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        WidgetStateInterface $widgetState = null
    ) {
        if ($widgetState && !$widgetState instanceof DefinedWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected a %s, got %s',
                    DefinedWidgetStateInterface::class,
                    get_class($widgetState)
                )
            );
        }

        return $this->definition->createEvaluationContextForWidget($parentContext, $this, $widgetState);
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag)
    {
        return $this->definition->createEvent($libraryName, $eventName, $payloadStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(
        $name,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        return $this->definition->createInitialStateForWidget(
            $name,
            $this,
            $this->attributeExpressions,
            $evaluationContext,
            $evaluationContextFactory
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
        if (!$widgetEvaluationContext instanceof DefinedWidgetEvaluationContextInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    DefinedWidgetEvaluationContextInterface::class,
                    get_class($widgetEvaluationContext)
                )
            );
        }

        // Widget must be visible - if it was not, its state would have been an InvisibleWidgetState,
        // whose ->invokeTriggersForEvent() method will do nothing

        if ($this->triggerCollection->isEmpty() && $this->parentWidget !== null) {
            // Widget has no triggers defined directly on it, so it implicitly forwards
            // any and all events to its parent widget (bubbling)
            return $this->parentWidget->dispatchEvent($programState, $program, $event, $widgetEvaluationContext);
        }

        if ($this->triggerCollection->hasByEventName($event->getLibraryName(), $event->getName())) {
            $trigger = $this->triggerCollection->getByEventName($event->getLibraryName(), $event->getName());

            $definitionSubEvaluationContext = $this->definition->createDefinitionEvaluationContextForWidget(
                $widgetEvaluationContext,
                $this,
                $widgetEvaluationContext->getWidgetState()
            );

            $programState = $trigger->invoke($programState, $program, $event, $definitionSubEvaluationContext);
        }

        return $programState;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attributeName, ViewEvaluationContextInterface $evaluationContext)
    {
        return $this->attributeExpressions->getExpression($attributeName)->toStatic($evaluationContext);
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
    public function getChildWidget($childName)
    {
        return $this->childWidgets[$childName];
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
        return $this->definition->getLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionName()
    {
        return $this->definition->getName();
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
        return $this->definition->isRenderable();
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateState(
        WidgetStateInterface $oldState,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        if (!$oldState instanceof DefinedWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    DefinedWidgetStateInterface::class,
                    get_class($oldState)
                )
            );
        }

        return $this->definition->reevaluateStateForWidget(
            $oldState,
            $this,
            $this->attributeExpressions,
            $evaluationContext,
            $evaluationContextFactory
        );
    }
}
