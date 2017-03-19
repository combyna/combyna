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

use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;

/**
 * Interface WidgetDefinitionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetDefinitionLoaderInterface
{
    /**
     * Creates a widget definition from a config array
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param array $widgetDefinitionConfig
     * @return WidgetDefinitionNodeInterface
     */
    public function load(
        $libraryName,
        $widgetDefinitionName,
        array $widgetDefinitionConfig
    );
}
