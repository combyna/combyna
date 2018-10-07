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
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
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
        WidgetStateInterface $widgetState
    ) {
        if (!$widgetState instanceof RepeaterWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected a %s, got %s',
                    RepeaterWidgetStateInterface::class,
                    get_class($widgetState)
                )
            );
        }

        return $evaluationContextFactory->createCoreWidgetEvaluationContext($parentContext, $this, $widgetState);
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
        ViewEvaluationContextInterface $evaluationContext
    ) {
        if ($this->repeatedWidget === null) {
            // This should never happen, but just in case
            throw new LogicException('Repeated widget has not been set for repeater');
        }

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

        // Create one instance of the repeated widget's state for each item in the list
        $repeatedWidgetStates = $itemStaticList->mapArray(
            $this->itemVariableName,
            $this->indexVariableName,
            function (
                ViewEvaluationContextInterface $itemEvaluationContext,
                StaticInterface $static,
                $index
            ) use ($evaluationContext) {
                // Use the index of each repeated instance as its name
                return $this->repeatedWidget->createInitialState(
                    $index,
                    $itemEvaluationContext
                );
            },
            $evaluationContext
        );

        $state = $this->uiStateFactory->createRepeaterWidgetState(
            $name,
            $this,
            $repeatedWidgetStates
        );

        return $state;
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
            'RepeaterWidgets cannot have attributes, so attribute "%s" cannot be fetched',
            $attributeName
        ));
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
    public function setRepeatedWidget(WidgetInterface $repeatedWidget)
    {
        $this->repeatedWidget = $repeatedWidget;
    }
}
