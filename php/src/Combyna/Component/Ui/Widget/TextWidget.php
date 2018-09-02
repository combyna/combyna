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
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use LogicException;

/**
 * Class TextWidget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextWidget implements TextWidgetInterface
{
    const DEFINITION = 'text';

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
     * @var ExpressionInterface
     */
    private $textExpression;

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
     * @param ExpressionInterface $textExpression
     * @param UiStateFactoryInterface $uiStateFactory
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     */
    public function __construct(
        WidgetInterface $parentWidget,
        $name,
        ExpressionInterface $textExpression,
        UiStateFactoryInterface $uiStateFactory,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
        $this->name = $name;
        $this->parentWidget = $parentWidget;
        $this->tags = $tags;
        $this->textExpression = $textExpression;
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
        throw new LogicException('TextWidgets cannot handle events');
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState($name, ViewEvaluationContextInterface $evaluationContext)
    {
        $textStatic = $this->textExpression->toStatic($evaluationContext);

        return $this->uiStateFactory->createTextWidgetState($name, $this, $textStatic->toNative());
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
        throw new LogicException('TextWidgets cannot handle events');
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attributeName, ViewEvaluationContextInterface $evaluationContext)
    {
        throw new LogicException(sprintf(
            'TextWidgets cannot have attributes, so attribute "%s" cannot be fetched',
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
        throw new LogicException('TextWidgets cannot have descendants');
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
        return true; // TextWidgets cannot be resolved further, so they are always renderable
    }
}
