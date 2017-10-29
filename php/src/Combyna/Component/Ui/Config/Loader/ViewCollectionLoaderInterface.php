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
use Combyna\Component\Ui\Config\Act\PageViewNode;
use Combyna\Component\Ui\Config\Act\ViewCollectionNode;

/**
 * Interface ViewCollectionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewCollectionLoaderInterface
{
    /**
     * Loads a collection of view ACT nodes from an associative array of names to config arrays
     *
     * @param array $viewsConfig
     * @param EnvironmentNode $environmentNode
     * @return PageViewNode[]
     */
    public function loadPageViews(array $viewsConfig, EnvironmentNode $environmentNode);
}
