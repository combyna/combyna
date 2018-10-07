<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

/**
 * Interface WidgetValueProviderProviderInterface
 *
 * Provides a way to define widget providers using native PHP logic
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetValueProviderProviderInterface
{
    /**
     * Fetches a locator for each widget provider to register
     *
     * @return WidgetValueProviderLocatorInterface[]
     */
    public function getWidgetValueProviderLocators();
}
