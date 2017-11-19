<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Loader;

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Signal\Config\Loader\SignalHandlerCollectionLoaderInterface;
use Combyna\Component\Store\Config\Loader\QueryCollectionLoaderInterface;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreNode;

/**
 * Class ViewStoreLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreLoader implements ViewStoreLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var FixedStaticBagModelLoaderInterface
     */
    private $fixedStaticBagModelLoader;

    /**
     * @var QueryCollectionLoaderInterface
     */
    private $queryCollectionLoader;
    /**
     * @var SignalHandlerCollectionLoaderInterface
     */
    private $signalHandlerCollectionLoader;

    /**
     * @param ConfigParser $configParser
     * @param FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader
     * @param QueryCollectionLoaderInterface $queryCollectionLoader
     * @param SignalHandlerCollectionLoaderInterface $signalHandlerCollectionLoader
     */
    public function __construct(
        ConfigParser $configParser,
        FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader,
        QueryCollectionLoaderInterface $queryCollectionLoader,
        SignalHandlerCollectionLoaderInterface $signalHandlerCollectionLoader
    ) {
        $this->configParser = $configParser;
        $this->fixedStaticBagModelLoader = $fixedStaticBagModelLoader;
        $this->queryCollectionLoader = $queryCollectionLoader;
        $this->signalHandlerCollectionLoader = $signalHandlerCollectionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $slotBagConfig = $this->configParser->getOptionalElement(
            $config,
            'slots',
            'view store slots',
            [],
            'array'
        );
        $slotBagModelNode = $this->fixedStaticBagModelLoader->load($slotBagConfig);

        $queryConfig = $this->configParser->getOptionalElement(
            $config,
            'queries',
            'view store queries',
            [],
            'array'
        );
        $queryNodes = $this->queryCollectionLoader->loadCollection($queryConfig);

        $signalHandlerConfig = $this->configParser->getOptionalElement(
            $config,
            'signal_handlers',
            'view store signal handlers',
            [],
            'array'
        );
        $signalHandlerNodes = $this->signalHandlerCollectionLoader->loadCollection($signalHandlerConfig);

        return new ViewStoreNode($slotBagModelNode, $queryNodes, $signalHandlerNodes);
    }
}
