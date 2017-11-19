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

/**
 * Interface WidgetCollectionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetCollectionLoader implements WidgetCollectionLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadWidgets(
        array $widgetCollectionConfig,
        WidgetLoaderInterface $widgetLoader,
        EnvironmentNode $environmentNode
    ) {
        $widgets = [];

        foreach ($widgetCollectionConfig as $widgetName => $widgetConfig) {
            $widgets[$widgetName] = $widgetLoader->loadWidget($widgetConfig, $environmentNode);
        }

        return $widgets;
    }
}
