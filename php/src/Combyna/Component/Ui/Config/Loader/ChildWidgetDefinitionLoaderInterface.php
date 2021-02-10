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
 * Interface ChildWidgetDefinitionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ChildWidgetDefinitionLoaderInterface
{
    /**
     * Creates a child widget definition from a config array
     *
     * @param string $name
     * @param array $childConfig
     * @return ChildWidgetDefinitionNode
     */
    public function loadChildWidgetDefinition($name, array $childConfig);
}
