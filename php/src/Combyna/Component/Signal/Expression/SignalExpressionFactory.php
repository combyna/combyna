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

use Combyna\Component\Expression\AbstractExpressionFactory;

/**
 * Class SignalExpressionFactory
 *
 * Creates expression or static expression objects related to signals
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalExpressionFactory extends AbstractExpressionFactory implements SignalExpressionFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createSignalPayloadExpression($staticName)
    {
        return new SignalPayloadExpression($staticName);
    }
}
