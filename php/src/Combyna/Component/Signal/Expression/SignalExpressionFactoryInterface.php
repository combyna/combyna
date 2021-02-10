<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Expression;

use Combyna\Component\Expression\ExpressionFactoryInterface;

/**
 * Interface SignalExpressionFactoryInterface
 *
 * Creates expression or static expression objects related to signals
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalExpressionFactoryInterface extends ExpressionFactoryInterface
{
    /**
     * Creates a new SignalPayloadExpression
     *
     * @param string $staticName
     * @return SignalPayloadExpression
     */
    public function createSignalPayloadExpression($staticName);
}
