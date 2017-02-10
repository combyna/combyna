<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Expression;

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
