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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use LogicException;

/**
 * Class ChildReferenceWidget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildReferenceWidget implements ChildReferenceWidgetInterface
{
    const DEFINITION = 'child';

    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var string
     */
    private $childName;

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
     * @var ExpressionInterface|null
     */
    private $visibilityExpression;

    /**
     * @param WidgetInterface $parentWidget
     * @param string|int $name
     * @param string $childName
     * @param BagFactoryInterface $bagFactory
     * @param UiStateFactoryInterface $uiStateFactory
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     */
    public function __construct(
        WidgetInterface $parentWidget,
        $name,
        $childName,
        BagFactoryInterface $bagFactory,
        UiStateFactoryInterface $uiStateFactory,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
        $this->bagFactory = $bagFactory;
        $this->childName = $childName;
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
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        return $evaluationContextFactory->createCoreWidgetEvaluationContext(
            $parentContext,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag)
    {
        throw new LogicException('ChildReferenceWidgets cannot handle events');
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(ViewEvaluationContextInterface $evaluationContext)
    {
        $childWidget = $evaluationContext->getChildWidget($this->childName);

        return $this->uiStateFactory->createChildReferenceWidgetState(
            $this,
            $childWidget->createInitialState($evaluationContext)
        );
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
        throw new LogicException('ChildReferenceWidgets cannot handle events');
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attributeName, ViewEvaluationContextInterface $evaluationContext)
    {
        throw new LogicException(sprintf(
            'ChildReferenceWidgets cannot have attributes, so attribute "%s" cannot be fetched',
            $attributeName
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getChildName()
    {
        return $this->childName;
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
        throw new LogicException('ChildReferenceWidgets cannot have descendants');
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
        return array_merge($this->parentWidget->getPath(), [$this->name]);
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
        return true; // ChildReferenceWidgets cannot be resolved further, so they are always renderable
    }
}
