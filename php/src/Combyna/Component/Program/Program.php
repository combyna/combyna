<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program;

use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\View\OverlayViewCollectionInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;

/**
 * Class Program
 *
 * A program is the internal representation of an app.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Program implements ProgramInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var OverlayViewCollectionInterface
     */
    private $overlayViewCollection;

    /**
     * @var PageViewCollectionInterface
     */
    private $pageViewCollection;

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    /**
     * @var EvaluationContextInterface
     */
    private $rootEvaluationContext;

    /**
     * @param EnvironmentInterface $environment
     * @param ResourceRepositoryInterface $resourceRepository
     * @param PageViewCollectionInterface $pageViewCollection
     * @param OverlayViewCollectionInterface $overlayViewCollection
     * @param EvaluationContextInterface $rootEvaluationContext
     */
    public function __construct(
        EnvironmentInterface $environment,
        ResourceRepositoryInterface $resourceRepository,
        PageViewCollectionInterface $pageViewCollection,
        OverlayViewCollectionInterface $overlayViewCollection,
        EvaluationContextInterface $rootEvaluationContext
    ) {
        $this->environment = $environment;
        $this->overlayViewCollection = $overlayViewCollection;
        $this->pageViewCollection = $pageViewCollection;
        $this->resourceRepository = $resourceRepository;
        $this->rootEvaluationContext = $rootEvaluationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageViewByName($name)
    {
        if ($this->pageViewCollection->hasView($name)) {
            return $this->pageViewCollection->getView($name);
        }

        if ($this->overlayViewCollection->hasView($name)) {
            return $this->overlayViewCollection->getView($name);
        }

        throw new NotFoundException(sprintf('No page with name "%s" found', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getRootEvaluationContext()
    {
        return $this->rootEvaluationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getSignalDefinitionByName($libraryName, $signalName)
    {
        return $this->resourceRepository->getSignalDefinitionByName($libraryName, $signalName);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetByPath(array $names)
    {
        $widget = $this->pageViewCollection->getWidgetByPath($names);

        if ($widget !== null) {
            return $widget;
        }

        $widget = $this->overlayViewCollection->getWidgetByPath($names);

        if ($widget !== null) {
            return $widget;
        }

        throw new NotFoundException('No widget with path "', implode('-', $names) . '" found');
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionByName($libraryName, $widgetDefinitionName)
    {
        // TODO: Fetch from ResourceRepository once that has a WidgetDefinitionRepository

        return $this->environment->getWidgetDefinitionByName($libraryName, $widgetDefinitionName);
    }

    /**
     * {@inheritdoc}
     */
    public function handleSignal(
        ProgramStateInterface $programState,
        SignalInterface $signal
    ) {
        // TODO: Dispatch to all EntityStores
        // ...

        // Dispatch to all ViewStores and WidgetStores
        return $this->pageViewCollection->handleSignal($programState, $signal, $this, $this->environment);
    }
}
