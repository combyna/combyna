<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Act;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Router\Config\Act\RouteNode;
use Combyna\Component\Signal\Config\Act\SignalDefinitionNode;
use Combyna\Component\Ui\Config\Act\PageViewNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class AppNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppNode extends AbstractActNode
{
    const TYPE = 'app';

    /**
     * @var HomeNode
     */
    private $homeNode;

    /**
     * @var OverlayViewNode[]
     */
    private $overlayViewNodes;

    /**
     * @var PageViewNode[]
     */
    private $pageViewNodes;

    /**
     * @var RouteNode[]
     */
    private $routeNodes;

    /**
     * @var array|SignalDefinitionNode[]
     */
    private $signalDefinitionNodes;

    /**
     * @param SignalDefinitionNode[] $signalDefinitionNodes
     * @param RouteNode[] $routeNodes
     * @param HomeNode $homeNode
     * @param PageViewNode[] $pageViewNodes
     * @param OverlayViewNode[] $overlayViewNodes
     */
    public function __construct(
        array $signalDefinitionNodes,
        array $routeNodes,
        HomeNode $homeNode,
        array $pageViewNodes,
        array $overlayViewNodes
    ) {
        $this->homeNode = $homeNode;
        $this->overlayViewNodes = $overlayViewNodes;
        $this->pageViewNodes = $pageViewNodes;
        $this->routeNodes = $routeNodes;
        $this->signalDefinitionNodes = $signalDefinitionNodes;
    }

    /**
     * Fetches the route and attributes to navigate to when the app first loads
     *
     * @return HomeNode
     */
    public function getHome()
    {
        return $this->homeNode;
    }

    /**
     * Fetches the collection of overlay views in the app
     *
     * @return OverlayViewNode[]
     */
    public function getOverlayViews()
    {
        return $this->overlayViewNodes;
    }

    /**
     * Fetches the collection of page views in the app
     *
     * @return PageViewNode[]
     */
    public function getPageViews()
    {
        return $this->pageViewNodes;
    }

    /**
     * Fetches the collection of routes in the app
     *
     * @return RouteNode[]
     */
    public function getRoutes()
    {
        return $this->routeNodes;
    }

    /**
     * Fetches the collection of signal definitions in the app
     *
     * @return SignalDefinitionNode[]
     */
    public function getSignalDefinitions()
    {
        return $this->signalDefinitionNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        foreach ($this->overlayViewNodes as $overlayViewNode) {
            $overlayViewNode->validate($subValidationContext);
        }

        foreach ($this->pageViewNodes as $pageViewNode) {
            $pageViewNode->validate($subValidationContext);
        }

        foreach ($this->routeNodes as $routeNode) {
            $routeNode->validate($subValidationContext);
        }
    }
}
