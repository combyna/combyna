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
use Combyna\Component\Ui\Evaluation\UiEvaluationContextInterface;
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
     * @var BagFactoryInterface
     */
    private $bagFactory;

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
     * @param BagFactoryInterface $bagFactory
     * @param UiStateFactoryInterface $uiStateFactory
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     */
    public function __construct(
        WidgetInterface $parentWidget,
        $name,
        ExpressionInterface $textExpression,
        BagFactoryInterface $bagFactory,
        UiStateFactoryInterface $uiStateFactory,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
        $this->bagFactory = $bagFactory;
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
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag)
    {
        throw new LogicException('TextWidgets cannot handle events');
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(
        UiEvaluationContextInterface $evaluationContext
    ) {
        $textStatic = $this->textExpression->toStatic($evaluationContext);

        return $this->uiStateFactory->createTextWidgetState(
            $this,
//            $this->bagFactory->createStaticBag([
//                'text' => $textStatic
//            ])
            $textStatic
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
        throw new LogicException('TextWidgets cannot handle events');
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
        return false; // TODO
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function render(
//        ViewEvaluationContextInterface $evaluationContext,
//        WidgetStateInterface $parentRenderedWidget = null
//    ) {
//        $textStatic = $this->textExpression->toStatic($evaluationContext);
//
//        return $this->uiStateFactory->createRenderedCoreWidget(
//            $parentRenderedWidget,
//            $this,
//            $this->bagFactory->createStaticBag([
//                'text' => $textStatic
//            ])
//        );
//    }
}
