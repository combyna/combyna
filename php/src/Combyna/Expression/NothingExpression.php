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
 * Class NothingExpression
 *
 * Represents a missing or unspecified value (rarely used)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NothingExpression extends AbstractStaticExpression
{
    const TYPE = 'nothing';

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return null;
    }
}
