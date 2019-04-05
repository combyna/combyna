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

/**
 * Interface CoreWidgetTypeLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface CoreWidgetTypeLoaderInterface
{
    /**
     * Fetches a map from core widget definition name to the loader callable on this service
     *
     * @return array
     */
    public function getWidgetDefinitionToLoaderCallableMap();
}
