<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
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
    public function loadPageViews(array $viewsConfig, EnvironmentNode $environmentNode)
    {
        $viewNodes = [];

        foreach ($viewsConfig as $viewName => $viewConfig) {
            $viewNodes[$viewName] = $this->viewLoader->loadPageView($viewName, $viewConfig, $environmentNode);
        }

        return $viewNodes;
    }
}
