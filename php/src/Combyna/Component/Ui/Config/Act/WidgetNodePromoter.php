<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Expression\Config\Act\DelegatingExpressionNodePromoter;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Trigger\Config\Act\TriggerNodePromoter;
use Combyna\Component\Ui\View\ViewFactoryInterface;
use Combyna\Component\Ui\Widget\ChildReferenceWidgetInterface;
use Combyna\Component\Ui\Widget\ConditionalWidget;
use Combyna\Component\Ui\Widget\ConditionalWidgetInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\RepeaterWidget;
use Combyna\Component\Ui\Widget\RepeaterWidgetInterface;
use Combyna\Component\Ui\Widget\TextWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use InvalidArgumentException;

/**
 * Class WidgetNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var DelegatingExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @var TriggerNodePromoter
     */
    private $triggerNodePromoter;

    /**
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * @param ViewFactoryInterface $viewFactory
     * @param BagNodePromoter $bagNodePromoter
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     * @param TriggerNodePromoter $triggerNodePromoter
     */
    public function __construct(
        ViewFactoryInterface $viewFactory,
        BagNodePromoter $bagNodePromoter,
        DelegatingExpressionNodePromoter $expressionNodePromoter,
        TriggerNodePromoter $triggerNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->triggerNodePromoter = $triggerNodePromoter;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Promotes a WidgetNode to a Widget
     *
     * @param string|int $name
     * @param WidgetNodeInterface $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return WidgetInterface
     */
    public function promoteWidget(
        $name,
        WidgetNodeInterface $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        if ($widgetNode instanceof WidgetGroupNode) {
            $widget = $this->promoteWidgetGroupNode($name, $widgetNode, $resourceRepository, $parentWidget);
        } elseif ($widgetNode instanceof TextWidgetNode) {
            $widget = $this->promoteTextWidgetNode($name, $widgetNode, $resourceRepository, $parentWidget);
        } elseif ($widgetNode instanceof DefinedWidgetNode) {
            $widget = $this->promoteDefinedWidgetNode($name, $widgetNode, $resourceRepository, $parentWidget);
        } elseif ($widgetNode instanceof ChildReferenceWidgetNode) {
            $widget = $this->promoteChildReferenceWidgetNode($name, $widgetNode, $parentWidget);
        } elseif ($widgetNode instanceof RepeaterWidgetNode) {
            $widget = $this->promoteRepeaterWidgetNode($name, $widgetNode, $resourceRepository, $parentWidget);
        } elseif ($widgetNode instanceof ConditionalWidgetNode) {
            $widget = $this->promoteConditionalWidgetNode($name, $widgetNode, $resourceRepository, $parentWidget);
        } else {
            throw new InvalidArgumentException(sprintf('Unsupported widget type "%s" given', $widgetNode->getType()));
        }

        return $widget;
    }

    /**
     * Promotes a ChildReferenceWidgetNode to the referenced child widget
     *
     * @param string|int $name
     * @param ChildReferenceWidgetNode $childReferenceWidgetNode
     * @param WidgetInterface|null $parentWidget
     * @return ChildReferenceWidgetInterface
     */
    private function promoteChildReferenceWidgetNode(
        $name,
        ChildReferenceWidgetNode $childReferenceWidgetNode,
        WidgetInterface $parentWidget = null
    ) {
        return $this->viewFactory->createChildReferenceWidget(
            $name,
            $childReferenceWidgetNode->getChildName(),
            $this->bagNodePromoter->promoteFixedStaticBagModel($childReferenceWidgetNode->getCaptureStaticBagModel()),
            $this->bagNodePromoter->promoteExpressionBag($childReferenceWidgetNode->getCaptureExpressionBag()),
            $parentWidget,
            $childReferenceWidgetNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($childReferenceWidgetNode->getVisibilityExpression()) :
                null,
            $childReferenceWidgetNode->getTags()
        );
    }

    /**
     * Promotes a ConditionalWidgetNode to a ConditionalWidget
     *
     * @param string|int $name
     * @param ConditionalWidgetNode $conditionalWidgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return ConditionalWidgetInterface
     */
    private function promoteConditionalWidgetNode(
        $name,
        ConditionalWidgetNode $conditionalWidgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $conditionalWidget = $this->viewFactory->createConditionalWidget(
            $name,
            $this->expressionNodePromoter->promote($conditionalWidgetNode->getConditionExpression()),
            $this->bagNodePromoter->promoteFixedStaticBagModel($conditionalWidgetNode->getCaptureStaticBagModel()),
            $this->bagNodePromoter->promoteExpressionBag($conditionalWidgetNode->getCaptureExpressionBag()),
            $parentWidget,
            $conditionalWidgetNode->getTags()
        );

        $conditionalWidget->setConsequentWidget(
            $this->promoteWidget(
                ConditionalWidget::CONSEQUENT_WIDGET_NAME,
                $conditionalWidgetNode->getConsequentWidget(),
                $resourceRepository,
                $conditionalWidget
            )
        );

        if ($conditionalWidgetNode->getAlternateWidget() !== null) {
            $conditionalWidget->setAlternateWidget(
                $this->promoteWidget(
                    ConditionalWidget::ALTERNATE_WIDGET_NAME,
                    $conditionalWidgetNode->getAlternateWidget(),
                    $resourceRepository,
                    $conditionalWidget
                )
            );
        }

        return $conditionalWidget;
    }

    /**
     * Promotes a DefinedWidgetNode to a DefinedWidget
     *
     * @param string|int $name
     * @param DefinedWidgetNode $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return DefinedWidgetInterface
     */
    private function promoteDefinedWidgetNode(
        $name,
        DefinedWidgetNode $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $widgetDefinition = $resourceRepository->getWidgetDefinitionByName(
            $widgetNode->getLibraryName(),
            $widgetNode->getWidgetDefinitionName()
        );
        $triggerCollection = $this->triggerNodePromoter->promoteCollection(
            $widgetNode->getTriggers(),
            $resourceRepository
        );

        $widget = $this->viewFactory->createDefinedWidget(
            $name,
            $widgetDefinition,
            $this->bagNodePromoter->promoteExpressionBag(
                $widgetNode->getAttributeExpressionBag()
            ),
            $triggerCollection,
            $this->bagNodePromoter->promoteFixedStaticBagModel($widgetNode->getCaptureStaticBagModel()),
            $this->bagNodePromoter->promoteExpressionBag($widgetNode->getCaptureExpressionBag()),
            $parentWidget,
            $widgetNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($widgetNode->getVisibilityExpression()) :
                null,
            $widgetNode->getTags()
        );

        foreach ($widgetNode->getChildWidgets() as $childWidgetName => $childWidgetNode) {
            $childWidget = $this->promoteWidget($childWidgetName, $childWidgetNode, $resourceRepository, $widget);

            $widget->addChildWidget($childWidget);
        }

        return $widget;
    }

    /**
     * Promotes a RepeaterWidgetNode to a RepeaterWidget
     *
     * @param string|int $name
     * @param RepeaterWidgetNode $repeaterWidgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return RepeaterWidgetInterface
     */
    private function promoteRepeaterWidgetNode(
        $name,
        RepeaterWidgetNode $repeaterWidgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $repeaterWidget = $this->viewFactory->createRepeaterWidget(
            $name,
            $this->expressionNodePromoter->promote($repeaterWidgetNode->getItemListExpression()),
            $repeaterWidgetNode->getIndexVariableName(),
            $repeaterWidgetNode->getItemVariableName(),
            $this->bagNodePromoter->promoteFixedStaticBagModel($repeaterWidgetNode->getCaptureStaticBagModel()),
            $this->bagNodePromoter->promoteExpressionBag($repeaterWidgetNode->getCaptureExpressionBag()),
            $parentWidget,
            $repeaterWidgetNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($repeaterWidgetNode->getVisibilityExpression()) :
                null,
            $repeaterWidgetNode->getTags()
        );

        $repeaterWidget->setRepeatedWidget(
            $this->promoteWidget(
                RepeaterWidget::REPEATED_WIDGET_NAME,
                $repeaterWidgetNode->getRepeatedWidget(),
                $resourceRepository,
                $repeaterWidget
            )
        );

        return $repeaterWidget;
    }

    /**
     * Promotes a TextWidgetNode to a TextWidget
     *
     * @param string|int $name
     * @param TextWidgetNode $textWidgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return TextWidgetInterface
     */
    private function promoteTextWidgetNode(
        $name,
        TextWidgetNode $textWidgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $textExpression = $this->expressionNodePromoter->promote($textWidgetNode->getTextExpression());

        return $this->viewFactory->createTextWidget(
            $name,
            $textExpression,
            $this->bagNodePromoter->promoteFixedStaticBagModel($textWidgetNode->getCaptureStaticBagModel()),
            $this->bagNodePromoter->promoteExpressionBag($textWidgetNode->getCaptureExpressionBag()),
            $parentWidget,
            $textWidgetNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($textWidgetNode->getVisibilityExpression()) :
                null,
            $textWidgetNode->getTags()
        );
    }

    /**
     * Promotes a WidgetGroupNode to a WidgetGroup
     *
     * @param string|int $name
     * @param WidgetGroupNode $widgetGroupNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return WidgetGroupInterface
     */
    private function promoteWidgetGroupNode(
        $name,
        WidgetGroupNode $widgetGroupNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $widget = $this->viewFactory->createWidgetGroup(
            $name,
            $this->bagNodePromoter->promoteFixedStaticBagModel($widgetGroupNode->getCaptureStaticBagModel()),
            $this->bagNodePromoter->promoteExpressionBag($widgetGroupNode->getCaptureExpressionBag()),
            $parentWidget,
            $widgetGroupNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($widgetGroupNode->getVisibilityExpression()) :
                null,
            $widgetGroupNode->getTags()
        );

        foreach ($widgetGroupNode->getChildWidgets() as $childWidgetName => $childWidgetNode) {
            $childWidget = $this->promoteWidget($childWidgetName, $childWidgetNode, $resourceRepository, $widget);

            $widget->addChildWidget($childWidget);
        }

        return $widget;
    }
}
