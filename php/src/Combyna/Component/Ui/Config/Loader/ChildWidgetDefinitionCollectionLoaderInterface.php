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

use Combyna\Component\Ui\Config\Act\ChildWidgetDefinitionNode;

/**
 * Interface ChildWidgetDefinitionCollectionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ChildWidgetDefinitionCollectionLoaderInterface
{
    /**
     * Loads a collection of child widget definition ACT nodes from an associative array of names to config arrays
     *
     * @param array $childrenConfig
     * @return ChildWidgetDefinitionNode[]
     */
    public function loadCollection(array $childrenConfig);
}
