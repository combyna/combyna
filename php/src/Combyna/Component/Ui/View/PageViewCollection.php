<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\View;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Signal\SignalInterface;
use InvalidArgumentException;

/**
 * Class PageViewCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PageViewCollection implements PageViewCollectionInterface
{
    /**
     * @var PageViewInterface[]
     */
    private $views;

    /**
     * @param PageViewInterface[] $views
     */
    public function __construct(array $views)
    {
        $viewsByName = [];

        // Ensure views are indexed by name
        foreach ($views as $view) {
            $viewsByName[$view->getName()] = $view;
        }

        $this->views = $viewsByName;
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(RouterStateInterface $routerState, EvaluationContextInterface $evaluationContext)
    {
        $pageViewName = $routerState->getRoutePageViewName();

        return $this->views[$pageViewName]->createInitialState($routerState, $evaluationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getView($viewName)
    {
        if (!$this->hasView($viewName)) {
            throw new InvalidArgumentException(sprintf('Collection has no view with name "%s"', $viewName));
        }

        return $this->views[$viewName];
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetByPath(array $names)
    {
        $viewName = array_shift($names);

        return $this->getView($viewName)->getWidgetByPath($names);
    }

    /**
     * {@inheritdoc}
     */
    public function handleSignal(
        ProgramStateInterface $programState,
        SignalInterface $signal,
        ProgramInterface $program,
        EnvironmentInterface $environment
    ) {
        $pageViewName = $programState->getRouterState()->getRoutePageViewName();

        $newPageViewState = $this->views[$pageViewName]->handleSignal(
            $programState->getPageViewState(),
            $signal,
            $program,
            $environment
        );

        return $programState->withPageViewState($newPageViewState);
    }

    /**
     * {@inheritdoc}
     */
    public function hasView($viewName)
    {
        return array_key_exists($viewName, $this->views);
    }
}
