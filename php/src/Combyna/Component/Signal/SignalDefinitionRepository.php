<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Environment\Library\LibraryInterface;

/**
 * Class SignalDefinitionRepository
 *
 * A facade to allow addressing all signal definitions defined by installed libraries or the app itself
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionRepository implements SignalDefinitionRepositoryInterface
{
    /**
     * @var SignalDefinitionCollectionInterface
     */
    private $appSignalDefinitionCollection;

    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @param EnvironmentInterface $environment
     * @param SignalDefinitionCollectionInterface $appSignalDefinitionCollection
     */
    public function __construct(
        EnvironmentInterface $environment,
        SignalDefinitionCollectionInterface $appSignalDefinitionCollection
    ) {
        $this->appSignalDefinitionCollection = $appSignalDefinitionCollection;
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($libraryName, $signalName)
    {
        if ($libraryName === LibraryInterface::APP) {
            return $this->appSignalDefinitionCollection->getByName($signalName);
        }

        return $this->environment->getSignalDefinitionByName($libraryName, $signalName);
    }
}
