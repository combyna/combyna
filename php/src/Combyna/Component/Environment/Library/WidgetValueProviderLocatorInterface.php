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
 * Interface WidgetValueProviderLocatorInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetValueProviderLocatorInterface
{
    /**
     * Fetches the callable to be called for the widget provider
     *
     * @return callable
     */
    public function getCallable();

    /**
     * Fetches the name of the library the function is installed in
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the name of the widget value
     *
     * @return string
     */
    public function getValueName();

    /**
     * Fetches the name of the widget definition
     *
     * @return string
     */
    public function getWidgetDefinitionName();
}
