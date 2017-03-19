<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Act;

/**
 * Class AbstractActNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractActNode implements ActNodeInterface
{
    const TYPE = 'act-node';

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return static::TYPE;
    }
}
