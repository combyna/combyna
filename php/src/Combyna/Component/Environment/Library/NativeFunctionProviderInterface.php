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
 * Interface NativeFunctionProviderInterface
 *
 * Provides a way to define custom functions using native PHP logic
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface NativeFunctionProviderInterface
{
    /**
     * Fetches a locator for each native function to register
     *
     * @return NativeFunctionLocatorInterface[]
     */
    public function getNativeFunctionLocators();
}
