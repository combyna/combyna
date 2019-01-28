<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program\State;

use Combyna\Component\Common\Exception\NonUniqueResultException;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\State\StateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\View\ViewStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;

/**
 * Interface ProgramStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ProgramStateInterface extends StateInterface
{
    const TYPE = 'program';

    /**
     * Fetches the state of the current page view
     *
     * @return PageViewStateInterface
     */
    public function getPageViewState();

    /**
     * Fetches the state of the router that this app state represents
     *
     * @return RouterStateInterface
     */
    public function getRouterState();

    /**
     * Fetches the state of the currently visible overlay views
     *
     * @return ViewStateInterface[]
     */
    public function getVisibleOverlayViewStates();

    /**
     * Fetches the state of the current page view and any visible overlay views
     *
     * @return ViewStateInterface[]
     */
    public function getVisibleViewStates();

    /**
     * Fetches a single widget path by the path to the widget.
     * If no widget exists with the given path, a NotFoundException will be thrown instead
     *
     * @param string[]|int[] $path
     * @return WidgetStatePathInterface
     * @throws NotFoundException
     */
    public function getWidgetStatePathByPath(array $path);

    /**
     * Fetches a single widget path by a tag on the widget.
     * If multiple widgets would match, then a NonUniqueResultException will be thrown,
     * but if no widget is found a NotFoundException will be thrown instead
     *
     * @param string $tag
     * @return WidgetStatePathInterface
     * @throws NonUniqueResultException
     * @throws NotFoundException
     */
    public function getWidgetStatePathByTag($tag);

    /**
     * Creates a new ProgramState, with its sub-states set to the specified ones
     *
     * @param RouterStateInterface $routerState
     * @param PageViewStateInterface $pageViewState
     * @return ProgramStateInterface
     */
    public function withPage(
        RouterStateInterface $routerState,
        PageViewStateInterface $pageViewState
    );

    /**
     * Either creates a new program state with the specified page view state
     * or just returns the current one, if it already has the same state
     *
     * @param PageViewStateInterface $newPageViewState
     * @return ProgramStateInterface
     */
    public function withPageViewState(PageViewStateInterface $newPageViewState);
}
