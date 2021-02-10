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
 * Interface NativeFunctionLocatorInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface NativeFunctionLocatorInterface
{
    /**
     * Fetches the callable to be called for the native function
     *
     * @return callable
     */
    public function getCallable();

    /**
     * Fetches the name of the function
     *
     * @return string
     */
    public function getFunctionName();

    /**
     * Fetches the name of the library the function is installed in
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the names of the Combyna parameter arguments to fetch, in PHP argument order
     *
     * @return string[]
     */
    public function getParameterNamesInArgumentOrder();
}
