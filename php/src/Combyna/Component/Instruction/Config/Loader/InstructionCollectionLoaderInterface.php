<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Instruction\Config\Loader;

use Combyna\Component\Instruction\Config\Act\InstructionNodeInterface;

/**
 * Interface InstructionCollectionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InstructionCollectionLoaderInterface
{
    /**
     * Creates an array of instruction ACT nodes from the specified config array
     *
     * @param array $config
     * @return InstructionNodeInterface[]
     */
    public function loadCollection(array $config);
}
