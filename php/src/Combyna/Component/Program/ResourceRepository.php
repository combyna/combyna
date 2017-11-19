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

/**
 * Class ResourceRepository
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResourceRepository implements ResourceRepositoryInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var SignalDefinitionRepositoryInterface
     */
    private $signalDefinitionRepository;

    /**
     * @param EnvironmentInterface $environment
     * @param SignalDefinitionRepositoryInterface $signalDefinitionRepository
     */
    public function __construct(
        EnvironmentInterface $environment,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository
    ) {
        $this->environment = $environment;
        $this->signalDefinitionRepository = $signalDefinitionRepository;
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
        return $this->environment->getWidgetDefinitionByName($libraryName, $widgetDefinitionName);
    }
}
