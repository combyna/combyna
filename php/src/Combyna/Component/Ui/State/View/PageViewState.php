<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\View;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\View\PageViewInterface;

/**
 * Class PageViewState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PageViewState implements PageViewStateInterface
{
    /**
     * @var WidgetStateInterface
     */
    private $rootWidgetState;

    /**
     * @var UiStateFactoryInterface
     */
    private $stateFactory;

    /**
     * @var ViewStoreStateInterface
     */
    private $storeState;

    /**
     * @var PageViewInterface
     */
    private $view;

    /**
     * @var StaticBagInterface
     */
    private $viewAttributeStaticBag;

    /**
     * @param UiStateFactoryInterface $stateFactory
     * @param PageViewInterface $view
     * @param ViewStoreStateInterface $storeState
     * @param WidgetStateInterface $rootWidgetState
     * @param StaticBagInterface $viewAttributeStaticBag
     */
    public function __construct(
        UiStateFactoryInterface $stateFactory,
        PageViewInterface $view,
        ViewStoreStateInterface $storeState,
        WidgetStateInterface $rootWidgetState,
        StaticBagInterface $viewAttributeStaticBag
    ) {
        $this->rootWidgetState = $rootWidgetState;
        $this->stateFactory = $stateFactory;
        $this->storeState = $storeState;

        // FIXME: Remove references from state objects back to the entities like this!
        $this->view = $view;

        $this->viewAttributeStaticBag = $viewAttributeStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeStaticBag()
    {
        return $this->viewAttributeStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootWidgetState()
    {
        return $this->rootWidgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function getStateName()
    {
        return $this->view->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreState()
    {
        return $this->storeState;
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
    public function getViewName()
    {
        return $this->view->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathByPath(array $path)
    {
        $viewName = array_shift($path);

        if ($viewName !== $this->getViewName()) {
            throw new NotFoundException(
                'View "' . $this->getViewName() . '" does not contain widget with path "' . implode('-', $path) . '"'
            );
        }

        return $this->rootWidgetState->getWidgetStatePathByPath($path, [$this], $this->stateFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathsByTag($tag)
    {
        return $this->rootWidgetState->getWidgetStatePathsByTag($tag, [$this], $this->stateFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function withRootWidgetState(WidgetStateInterface $newRootWidgetState)
    {
        if ($this->rootWidgetState === $newRootWidgetState) {
            // We already have the provided sub-states, no need to create a new view state
            return $this;
        }

        // Otherwise create a new page view state, but with the new store state
        return new self(
            $this->stateFactory,
            $this->view,
            $this->storeState,
            $newRootWidgetState,
            $this->viewAttributeStaticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withState(ViewStoreStateInterface $newStoreState, WidgetStateInterface $newRootWidgetState)
    {
        if ($this->storeState === $newStoreState && $this->rootWidgetState === $newRootWidgetState) {
            // We already have the provided sub-states, no need to create a new view state
            return $this;
        }

        // Otherwise create a new page view state, but with the new store state
        return new self(
            $this->stateFactory,
            $this->view,
            $newStoreState,
            $newRootWidgetState,
            $this->viewAttributeStaticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withStoreState(ViewStoreStateInterface $newStoreState)
    {
        if ($this->storeState === $newStoreState) {
            // We already have the provided store state, no need to create a new view state
            return $this;
        }

        // Otherwise create a new page view state, but with the new store state
        return new self(
            $this->stateFactory,
            $this->view,
            $newStoreState,
            $this->rootWidgetState,
            $this->viewAttributeStaticBag
        );
    }
}
