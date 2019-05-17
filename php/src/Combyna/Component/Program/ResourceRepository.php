<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionRepositoryInterface;

/**
 * Class ResourceRepository
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResourceRepository implements RootResourceRepositoryInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var SignalDefinitionRepositoryInterface|null
     */
    private $signalDefinitionRepository;

    /**
     * @var WidgetDefinitionRepositoryInterface|null
     */
    private $widgetDefinitionRepository;

    /**
     * @param EnvironmentInterface $environment
     */
    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDefinitionByName($libraryName, $eventName)
    {
        return $this->environment->getEventDefinitionByName($libraryName, $eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function getSignalDefinitionByName($libraryName, $signalName)
    {
        return $this->signalDefinitionRepository->getByName($libraryName, $signalName);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionByName($libraryName, $widgetDefinitionName)
    {
        return $this->widgetDefinitionRepository->getByName($libraryName, $widgetDefinitionName);
    }

    /**
     * {@inheritdoc}
     */
    public function setSignalDefinitionRepository(SignalDefinitionRepositoryInterface $signalDefinitionRepository)
    {
        $this->signalDefinitionRepository = $signalDefinitionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function setWidgetDefinitionRepository(WidgetDefinitionRepositoryInterface $widgetDefinitionRepository)
    {
        $this->widgetDefinitionRepository = $widgetDefinitionRepository;
    }
}
