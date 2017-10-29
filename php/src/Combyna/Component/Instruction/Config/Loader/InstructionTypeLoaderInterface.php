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

/**
 * Interface InstructionTypeLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InstructionTypeLoaderInterface extends InstructionLoaderInterface
{
    /**
     * Fetches the type of instruction this loader can load
     *
     * @return string
     */
    public function getType();
}
