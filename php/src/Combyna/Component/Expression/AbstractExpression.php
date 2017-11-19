<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

/**
 * Class AbstractExpression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractExpression implements ExpressionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return static::TYPE;
    }
}
