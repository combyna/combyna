<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Signal\Exception\SignalDefinitionNotFoundException;

/**
 * Interface SignalDefinitionRepositoryInterface
 *
 * A facade to allow addressing all signal definitions defined by installed libraries or the app itself
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalDefinitionRepositoryInterface
{
    /**
     * Fetches a signal definition with the given name from the current app or a library in the environment
     *
     * @param string $libraryName
     * @param string $signalName
     * @return SignalDefinitionInterface
     * @throws LibraryNotInstalledException
     * @throws SignalDefinitionNotFoundException
     */
    public function getByName($libraryName, $signalName);
}
