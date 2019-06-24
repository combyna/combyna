<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Act;

use Combyna\Component\App\Validation\Context\Specifier\AppContextSpecifier;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Act\FunctionNodeInterface;
use Combyna\Component\Environment\Config\Act\UnknownFunctionNode;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Framework\Config\Act\RootNodeInterface;
use Combyna\Component\Router\Config\Act\RouteNode;
use Combyna\Component\Signal\Config\Act\SignalDefinitionNodeInterface;
use Combyna\Component\Signal\Config\Act\UnknownSignalDefinitionNode;
use Combyna\Component\Ui\Config\Act\PageViewNode;
use Combyna\Component\Ui\Config\Act\UnknownWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;

/**
 * Class AppNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppNode extends AbstractActNode implements RootNodeInterface
{
    const TYPE = 'app';

    /**
     * @var EnvironmentNode
     */
    private $environmentNode;

    /**
     * @var HomeNode
     */
    private $homeNode;

    /**
     * @var OverlayViewNode[]
     */
    private $overlayViewNodes = [];

    /**
     * @var PageViewNode[]
     */
    private $pageViewNodes = [];

    /**
     * @var RouteNode[]
     */
    private $routeNodes = [];

    /**
     * @var SignalDefinitionNodeInterface[]
     */
    private $signalDefinitionNodes = [];

    /**
     * @var WidgetDefinitionNodeInterface[]
     */
    private $widgetDefinitionNodes = [];

    /**
     * @param EnvironmentNode $environmentNode
     * @param SignalDefinitionNodeInterface[] $signalDefinitionNodes
     * @param WidgetDefinitionNodeInterface[] $widgetDefinitionNodes
     * @param RouteNode[] $routeNodes
     * @param HomeNode $homeNode
     * @param PageViewNode[] $pageViewNodes
     * @param OverlayViewNode[] $overlayViewNodes
     */
    public function __construct(
        EnvironmentNode $environmentNode,
        array $signalDefinitionNodes,
        array $widgetDefinitionNodes,
        array $routeNodes,
        HomeNode $homeNode,
        array $pageViewNodes,
        array $overlayViewNodes
    ) {
        $this->environmentNode = $environmentNode;
        $this->homeNode = $homeNode;

        // Index page views name to simplify lookups
        foreach ($pageViewNodes as $pageViewNode) {
            $this->pageViewNodes[$pageViewNode->getName()] = $pageViewNode;
        }

        // Index routes by name to simplify lookups
        foreach ($routeNodes as $routeNode) {
            $this->routeNodes[$routeNode->getName()] = $routeNode;
        }

        // Index signal definitions by name to simplify lookups
        foreach ($signalDefinitionNodes as $signalDefinitionNode) {
            $this->signalDefinitionNodes[$signalDefinitionNode->getSignalName()] = $signalDefinitionNode;
        }

        // Index widget definitions by name to simplify lookups
        foreach ($widgetDefinitionNodes as $widgetDefinitionNode) {
            $this->widgetDefinitionNodes[$widgetDefinitionNode->getWidgetDefinitionName()] = $widgetDefinitionNode;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        /*
         * Don't add the environment node as a child of this one, as we want to validate the app
         * as though it was a child of the environment instead - the app can depend on resources
         * from the environment, but the environment cannot depend on resources from the app
         */
        $specBuilder->defineValidationContext(new AppContextSpecifier($this->environmentNode));

        $specBuilder->addChildNode($this->homeNode);

        foreach ($this->overlayViewNodes as $overlayViewNode) {
            $specBuilder->addChildNode($overlayViewNode);
        }

        foreach ($this->pageViewNodes as $pageViewNode) {
            $specBuilder->addChildNode($pageViewNode);
        }

        foreach ($this->routeNodes as $routeNode) {
            $specBuilder->addChildNode($routeNode);
        }

        foreach ($this->signalDefinitionNodes as $signalDefinitionNode) {
            $specBuilder->addChildNode($signalDefinitionNode);
        }

        foreach ($this->widgetDefinitionNodes as $widgetDefinitionNode) {
            $specBuilder->addChildNode($widgetDefinitionNode);
        }
    }

    /**
     * Fetches the environment the app is to run inside
     *
     * @return EnvironmentNode
     */
    public function getEnvironment()
    {
        return $this->environmentNode;
    }

    /**
     * Fetches a function defined by the app.
     * If it does not define the specified function,
     * then an UnknownFunctionNode will be returned
     *
     * @param string $libraryName
     * @param string $functionName
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     * @return FunctionNodeInterface
     */
    public function getGenericFunction($libraryName, $functionName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        // TODO: Not yet implemented - only the libraries in the environment can define functions for now

        return new UnknownFunctionNode(
            $libraryName,
            $functionName,
            $dynamicActNodeAdopter
        );
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
     * Fetches a single signal definition in the app
     *
     * @param string $signalName
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     * @return SignalDefinitionNodeInterface
     */
    public function getSignalDefinition($signalName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        return array_key_exists($signalName, $this->signalDefinitionNodes) ?
            $this->signalDefinitionNodes[$signalName] :
            new UnknownSignalDefinitionNode(
                LibraryInterface::APP,
                $signalName,
                $dynamicActNodeAdopter
            );
    }

    /**
     * Fetches the collection of signal definitions in the app
     *
     * @return SignalDefinitionNodeInterface[]
     */
    public function getSignalDefinitions()
    {
        return $this->signalDefinitionNodes;
    }

    /**
     * Fetches a widget definition defined by the app.
     * If it does not define the specified definition,
     * then an UnknownWidgetDefinitionNode will be returned
     *
     * @param string $name
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     * @return WidgetDefinitionNodeInterface
     */
    public function getWidgetDefinition($name, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        return array_key_exists($name, $this->widgetDefinitionNodes) ?
            $this->widgetDefinitionNodes[$name] :
            new UnknownWidgetDefinitionNode(
                LibraryInterface::APP,
                $name,
                $dynamicActNodeAdopter
            );
    }

    /**
     * Fetches the collection of widget definitions in the app
     *
     * @return WidgetDefinitionNodeInterface[]
     */
    public function getWidgetDefinitions()
    {
        return $this->widgetDefinitionNodes;
    }
}
