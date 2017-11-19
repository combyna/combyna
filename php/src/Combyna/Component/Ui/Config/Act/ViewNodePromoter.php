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
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Config\Act\DelegatingExpressionNodePromoter;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreNodePromoter;
use Combyna\Component\Ui\View\EmbedViewInterface;
use Combyna\Component\Ui\View\OverlayViewCollectionInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;
use Combyna\Component\Ui\View\PageViewInterface;
use Combyna\Component\Ui\View\ViewFactoryInterface;

/**
 * Class ViewNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewNodePromoter
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
     * @var ViewStoreNodePromoter
     */
    private $storeNodePromoter;

    /**
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * @var WidgetNodePromoter
     */
    private $widgetNodePromoter;

    /**
     * @param ViewFactoryInterface $viewFactory
     * @param BagNodePromoter $bagNodePromoter
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     * @param ViewStoreNodePromoter $storeNodePromoter
     * @param WidgetNodePromoter $widgetNodePromoter
     */
    public function __construct(
        ViewFactoryInterface $viewFactory,
        BagNodePromoter $bagNodePromoter,
        DelegatingExpressionNodePromoter $expressionNodePromoter,
        ViewStoreNodePromoter $storeNodePromoter,
        WidgetNodePromoter $widgetNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->storeNodePromoter = $storeNodePromoter;
        $this->viewFactory = $viewFactory;
        $this->widgetNodePromoter = $widgetNodePromoter;
    }

    /**
     * Promotes an EmbedViewNode to an EmbedView
     *
     * @param EmbedViewNode $viewNode
     * @param EnvironmentInterface $environment
     * @return EmbedViewInterface
     */
    public function promoteEmbedView(
        EmbedViewNode $viewNode,
        EnvironmentInterface $environment
    ) {
        return $this->viewFactory->createEmbedView(
            $viewNode->getName(),
            $this->expressionNodePromoter->promote($viewNode->getTitleExpression()),
            $viewNode->getDescription(),
            $this->bagNodePromoter->promoteFixedStaticBagModel(
                $viewNode->getAttributeBagModel()
            ),
            $this->promoteWidget($viewNode->getRootWidget(), $environment),
            $viewNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($viewNode->getVisibilityExpression()) :
                null
        );
    }

    /**
     * Promotes a collection of OverlayViewNodes to an OverlayViewCollection
     *
     * @param OverlayViewNode[] $overlayViewNodes
     * @param ResourceRepositoryInterface $resourceRepository
     * @return OverlayViewCollectionInterface
     */
    public function promoteOverlayViewCollection(
        array $overlayViewNodes,
        ResourceRepositoryInterface $resourceRepository
    ) {
        $views = [];

        foreach ($overlayViewNodes as $overlayViewNode) {
            $views[] = $this->promoteOverlayView($overlayViewNode, $resourceRepository);
        }

        return $this->viewFactory->createOverlayViewCollection($views);
    }

    /**
     * Promotes a PageViewNode to a PageView
     *
     * @param PageViewNode $viewNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @return PageViewInterface
     */
    public function promotePageView(
        PageViewNode $viewNode,
        ResourceRepositoryInterface $resourceRepository
    ) {
        return $this->viewFactory->createPageView(
            $viewNode->getName(),
            $this->expressionNodePromoter->promote($viewNode->getTitleExpression()),
            $viewNode->getDescription(),
            $this->widgetNodePromoter->promoteWidget('root', $viewNode->getRootWidget(), $resourceRepository),
            $this->storeNodePromoter->promote($viewNode->getName(), $viewNode->getStore())
        );
    }

    /**
     * Promotes a collection of PageViewNodes to a PageViewCollection
     *
     * @param PageViewNode[] $pageViewNodes
     * @param ResourceRepositoryInterface $resourceRepository
     * @return PageViewCollectionInterface
     */
    public function promotePageViewCollection(
        array $pageViewNodes,
        ResourceRepositoryInterface $resourceRepository
    ) {
        $views = [];

        foreach ($pageViewNodes as $pageViewNode) {
            $views[] = $this->promotePageView($pageViewNode, $resourceRepository);
        }

        return $this->viewFactory->createPageViewCollection($views);
    }
}
