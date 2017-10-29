<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\View;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use InvalidArgumentException;

/**
 * Interface OverlayViewCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class OverlayViewCollection implements OverlayViewCollectionInterface
{
    /**
     * @var OverlayViewInterface[]
     */
    private $views;

    /**
     * @param OverlayViewInterface[] $views
     */
    public function __construct(array $views)
    {
        $this->views = $views;
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialStates()
    {
        $viewStates = [];

        foreach ($this->views as $overlayView) {
            $viewStates[$overlayView->getName()] = $overlayView->createInitialState();
        }

        return $viewStates;
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
    public function hasView($viewName)
    {
        return array_key_exists($viewName, $this->views);
    }

    /**
     * {@inheritdoc}
     */
    public function renderView(
        $viewName,
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $rootEvaluationContext
    ) {
        // TODO: Implement renderView() method.
    }
}
