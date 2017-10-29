<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program;

use Combyna\Component\Signal\SignalDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;

/**
 * Interface ResourceRepositoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ResourceRepositoryInterface
{
    /**
     * Fetches a signal definition with the given name from the current app or a library in the environment
     *
     * @param string $libraryName
     * @param string $signalName
     * @return SignalDefinitionInterface
     */
    public function getSignalDefinitionByName($libraryName, $signalName);

    /**
     * Fetches a widget definition with the given name from a library in the environment
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @return WidgetDefinitionInterface
     */
    public function getWidgetDefinitionByName($libraryName, $widgetDefinitionName);
}
