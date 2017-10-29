<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

/**
 * Class FunctionFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionFactory implements FunctionFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createCollection(array $functions, $libraryName)
    {
        return new FunctionCollection($functions, $libraryName);
    }
}
