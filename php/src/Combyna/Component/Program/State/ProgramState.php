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
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\View\ViewStateInterface;

/**
 * Class ProgramState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ProgramState implements ProgramStateInterface
{
    /**
     * @var PageViewStateInterface
     */
    private $pageViewState;

    /**
     * @var RouterStateInterface
     */
    private $routerState;

    /**
     * @var ViewStateInterface[]
     */
    private $visibleOverlayViewStates;

    /**
     * @param RouterStateInterface $routerState
     * @param PageViewStateInterface $pageViewState
     * @param ViewStateInterface[] $visibleOverlayViewStates
     */
    public function __construct(
        RouterStateInterface $routerState,
        PageViewStateInterface $pageViewState,
        array $visibleOverlayViewStates
    ) {
        $this->pageViewState = $pageViewState;
        $this->routerState = $routerState;
        $this->visibleOverlayViewStates = $visibleOverlayViewStates;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageViewState()
    {
        return $this->pageViewState;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouterState()
    {
        return $this->routerState;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibleViewStates()
    {
        return array_merge([$this->pageViewState], $this->visibleOverlayViewStates);
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibleOverlayViewStates()
    {
        return $this->visibleOverlayViewStates;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathByPath(array $path)
    {
        try {
            return $this->pageViewState->getWidgetStatePathByPath($path);
        } catch (NotFoundException $exception) {
        }

        foreach ($this->visibleOverlayViewStates as $overlayViewState) {
            try {
                return $overlayViewState->getWidgetStatePathByPath($path);
            } catch (NotFoundException $exception) {
            }
        }

        throw new NotFoundException('No widget exists with path "' . implode('-', $path) . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathByTag($tag)
    {
        $widgetStatePaths = $this->getWidgetStatePathsByTag($tag);

        if (count($widgetStatePaths) > 1) {
            throw new NonUniqueResultException(
                'Expected to find one widget with tag "' . $tag . '", but found ' . count($widgetStatePaths)
            );
        }

        if (count($widgetStatePaths) === 0) {
            throw new NotFoundException(
                'Expected to find one widget with tag "' . $tag . '", but found none'
            );
        }

        return $widgetStatePaths[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathsByTag($tag)
    {
        $widgetStatePaths = $this->pageViewState->getWidgetStatePathsByTag($tag);

        foreach ($this->visibleOverlayViewStates as $overlayViewState) {
            $widgetStatePaths = array_merge($widgetStatePaths, $overlayViewState->getWidgetStatePathsByTag($tag));
        }

        return $widgetStatePaths;
    }

    /**
     * {@inheritdoc}
     */
    public function withPage(
        RouterStateInterface $routerState,
        PageViewStateInterface $pageViewState
    ) {
        return new self($routerState, $pageViewState, $this->visibleOverlayViewStates);
    }

    /**
     * {@inheritdoc}
     */
    public function withPageViewState(PageViewStateInterface $newPageViewState)
    {
        if ($this->pageViewState === $newPageViewState) {
            // We already have the provided page view state, no need to create a new program state
            return $this;
        }

        return new self(
            $this->routerState,
            $newPageViewState,
            $this->visibleOverlayViewStates
        );
    }
}
