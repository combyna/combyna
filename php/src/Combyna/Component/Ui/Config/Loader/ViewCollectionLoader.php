<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader;

use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Ui\Config\Act\ViewCollectionNode;

/**
 * Class ViewCollectionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewCollectionLoader implements ViewCollectionLoaderInterface
{
    /**
     * @var ViewLoaderInterface
     */
    private $viewLoader;

    /**
     * @param ViewLoaderInterface $viewLoader
     */
    public function __construct(ViewLoaderInterface $viewLoader)
    {
        $this->viewLoader = $viewLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadViews(array $viewsConfig, EnvironmentNode $environmentNode)
    {
        $viewNodes = [];

        foreach ($viewsConfig as $viewName => $viewConfig) {
            $viewNodes[$viewName] = $this->viewLoader->loadView($viewName, $viewConfig, $environmentNode);
        }

        return new ViewCollectionNode($viewNodes);
    }
}
