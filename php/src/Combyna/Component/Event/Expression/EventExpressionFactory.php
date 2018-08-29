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

use Combyna\Component\Expression\AbstractExpressionFactory;

/**
 * Class EventExpressionFactory
 *
 * Creates expression or static expression objects related to events
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventExpressionFactory extends AbstractExpressionFactory implements EventExpressionFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createEventPayloadExpression($staticName)
    {
        return new EventPayloadExpression($this, $staticName);
    }
}
