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
     * @param EnvironmentNode $environmentNode
     * @return WidgetNodeInterface[]
     */
    public function loadWidgets(
        array $widgetCollectionConfig,
        WidgetLoaderInterface $widgetLoader,
        EnvironmentNode $environmentNode
    );
}
