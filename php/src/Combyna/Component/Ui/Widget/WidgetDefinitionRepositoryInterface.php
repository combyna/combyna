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

use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;

/**
 * Interface WidgetDefinitionRepositoryInterface
 *
 * A facade to allow addressing all widget definitions defined by installed libraries or the app itself
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetDefinitionRepositoryInterface
{
    /**
     * Fetches a widget definition with the given name from the current app or a library in the environment
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @return WidgetDefinitionInterface
     * @throws LibraryNotInstalledException
     * @throws WidgetDefinitionNotSupportedException
     */
    public function getByName($libraryName, $widgetDefinitionName);
}
