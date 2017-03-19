<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Act;

use Combyna\Component\Environment\Exception\NativeFunctionNotInstalledException;
use Combyna\Component\Environment\Library\FunctionInterface;
use RuntimeException;

/**
 * Class FunctionPromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionPromoter
{
    /**
     * Creates a Function from its ACT node
     *
     * @param FunctionNodeInterface $functionNode
     * @return FunctionInterface
     * @throws NativeFunctionNotInstalledException
     */
    public function promoteFunction(FunctionNodeInterface $functionNode)
    {
        if ($functionNode instanceof NativeFunctionNode) {
            return $functionNode->getNativeFunction();
        }

        throw new RuntimeException('Only native functions are supported for now');
    }
}
