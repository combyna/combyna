<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Widget;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;

/**
 * Class WidgetGroup
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetGroup implements WidgetGroupInterface
{
    const DEFINITION = 'widget-group';

    /**
     * @var WidgetInterface[]
     */
    private $childWidgets = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var WidgetInterface|null
     */
    private $parentWidget;

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
     * @param string $name
     * @param WidgetInterface|null $parentWidget
     * @param ExpressionInterface|null $visibilityExpression
     */
    public function __construct(
        UiStateFactoryInterface $uiStateFactory,
        $name,
        WidgetInterface $parentWidget = null,
        ExpressionInterface $visibilityExpression = null
    ) {
        $this->name = $name;
        $this->parentWidget = $parentWidget;
        $this->uiStateFactory = $uiStateFactory;
        $this->visibilityExpression = $visibilityExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function addChildWidget(WidgetInterface $childWidget)
    {
        $this->childWidgets[] = $childWidget;
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
        UiEvaluationContextInterface $evaluationContext
    ) {
        $state = $this->uiStateFactory->createWidgetGroupState($this);

        foreach ($this->childWidgets as $childWidget) {
            $state->addChild($childWidget->createInitialState($evaluationContext));
        }

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
        throw new \Exception('Not implemented');
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
        return false; // TODO
    }
}
