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

use Combyna\Component\Ui\Config\Act\WidgetNodeInterface;

/**
 * Interface WidgetCollectionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetCollectionLoaderInterface
{
    /**
     * Creates a collection of widgets from a config array
     *
     * @param array $widgetCollectionConfig
     * @param WidgetLoaderInterface $widgetLoader
     * @return WidgetNodeInterface[]
     */
    public function loadWidgets(
        array $widgetCollectionConfig,
        WidgetLoaderInterface $widgetLoader
    );
}
