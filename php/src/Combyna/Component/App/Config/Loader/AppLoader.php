<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Loader;

use Combyna\Component\App\AppFactoryInterface;
use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Ui\Config\Loader\ViewCollectionLoaderInterface;

/**
 * Class AppLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppLoader implements AppLoaderInterface
{
    /**
     * @var AppFactoryInterface
     */
    private $appFactory;

    /**
     * @var ViewCollectionLoaderInterface
     */
    private $viewCollectionLoader;

    /**
     * @param AppFactoryInterface $appFactory
     * @param ViewCollectionLoaderInterface $viewCollectionLoader
     */
    public function __construct(
        AppFactoryInterface $appFactory,
        ViewCollectionLoaderInterface $viewCollectionLoader
    ) {
        $this->appFactory = $appFactory;
        $this->viewCollectionLoader = $viewCollectionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadApp(EnvironmentNode $environmentNode, array $appConfig)
    {
        $viewCollectionNode = $this->viewCollectionLoader->loadViews(
            $appConfig['views'],
            $environmentNode
        );

        return new AppNode($viewCollectionNode);
    }
}
