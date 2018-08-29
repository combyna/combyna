<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Trigger\Behaviour\Query\Specifier\InsideTriggerQuerySpecifier;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class InsideTriggerQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InsideTriggerQuery implements BooleanQueryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultResult()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Whether we are inside a trigger';
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $querySpecifier instanceof InsideTriggerQuerySpecifier;
    }
}
