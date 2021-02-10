<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Widget;

use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;

/**
 * Interface WidgetDefinitionCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetDefinitionCollectionInterface
{
    /**
     * Fetches a widget definition from this collection by its unique name
     *
     * @param string $widgetName
     * @return WidgetDefinitionInterface
     * @throws WidgetDefinitionNotSupportedException Throws when no definition has the specified name
     */
    public function getByName($widgetName);
}
