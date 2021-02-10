<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Expression;

use Combyna\Component\Expression\ExpressionFactoryInterface;

/**
 * Interface EventExpressionFactoryInterface
 *
 * Creates expression or static expression objects related to events
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventExpressionFactoryInterface extends ExpressionFactoryInterface
{
    /**
     * Creates a new EventPayloadExpression
     *
     * @param string $staticName
     * @return EventPayloadExpression
     */
    public function createEventPayloadExpression($staticName);
}
