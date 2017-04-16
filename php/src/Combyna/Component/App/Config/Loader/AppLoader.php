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
use Symfony\Component\Translation\Translator;

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
     * @var Translator
     */
    private $translator;

    /**
     * @var ViewCollectionLoaderInterface
     */
    private $viewCollectionLoader;

    /**
     * @param AppFactoryInterface $appFactory
     * @param ViewCollectionLoaderInterface $viewCollectionLoader
     * @param Translator $translator
     */
    public function __construct(
        AppFactoryInterface $appFactory,
        ViewCollectionLoaderInterface $viewCollectionLoader,
        Translator $translator
    ) {
        $this->appFactory = $appFactory;
        $this->translator = $translator;
        $this->viewCollectionLoader = $viewCollectionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadApp(EnvironmentNode $environmentNode, array $appConfig)
    {
        // Load any translations from the app config into Symfony's translator
        // so that they will be available later
        if (array_key_exists('translations', $appConfig)) {
            foreach ($appConfig['translations'] as $locale => $messages) {
                $this->translator->addResource('array', $messages, $locale);
            }
        }

        $viewCollectionNode = $this->viewCollectionLoader->loadViews(
            $appConfig['views'],
            $environmentNode
        );

        return new AppNode($viewCollectionNode);
    }
}
