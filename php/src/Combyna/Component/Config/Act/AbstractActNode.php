<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Act;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;

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
    public function getIdentifier()
    {
        return $this->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
