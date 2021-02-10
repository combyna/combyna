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
 * Interface WidgetLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetLoaderInterface
{
    /**
     * Creates a widget from a config array
     *
     * @param array $widgetConfig
     * @param string|null $name
     * @return WidgetNodeInterface
     */
    public function loadWidget(array $widgetConfig, $name = null);
}
