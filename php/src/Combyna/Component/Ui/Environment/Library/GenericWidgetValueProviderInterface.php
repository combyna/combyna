<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Environment\Library;

use Combyna\Component\Environment\Library\WidgetValueProviderProviderInterface;

/**
 * Interface GenericWidgetValueProviderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface GenericWidgetValueProviderInterface extends WidgetValueProviderProviderInterface
{
    /**
     * Adds a provider for a specific widget value of a primitive widget definition
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param string $valueName
     * @param callable $callable
     */
    public function addProvider($libraryName, $widgetDefinitionName, $valueName, callable $callable);
}
