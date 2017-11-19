<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Loader;

/**
 * Class SignalHandlerCollectionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalHandlerCollectionLoader implements SignalHandlerCollectionLoaderInterface
{
    /**
     * @var SignalHandlerLoaderInterface
     */
    private $signalHandlerLoader;

    /**
     * @param SignalHandlerLoaderInterface $signalHandlerLoader
     */
    public function __construct(SignalHandlerLoaderInterface $signalHandlerLoader)
    {
        $this->signalHandlerLoader = $signalHandlerLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadCollection(array $signalHandlerConfigs)
    {
        $signalHandlerNodes = [];

        foreach ($signalHandlerConfigs as $signalHandlerReferenceName => $signalHandlerConfig) {
            list($signalLibraryName, $signalName) = explode('.', $signalHandlerReferenceName);

            $signalHandlerNodes[] = $this->signalHandlerLoader->load(
                $signalLibraryName,
                $signalName,
                $signalHandlerConfig
            );
        }

        return $signalHandlerNodes;
    }
}
