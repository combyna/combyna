<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Loader\Library;

use Combyna\Component\Environment\Config\Act\LibraryNode;

/**
 * Interface LibraryLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface LibraryLoaderInterface
{
    /**
     * Creates an LibraryNode from the given environment config
     *
     * @param array $libraryConfig
     * @return LibraryNode
     */
    public function loadLibrary(array $libraryConfig);
}
