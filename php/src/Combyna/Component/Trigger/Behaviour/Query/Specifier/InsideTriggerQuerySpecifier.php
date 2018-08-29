<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Behaviour\Query\Specifier;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;

/**
 * Class InsideTriggerQuerySpecifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InsideTriggerQuerySpecifier implements QuerySpecifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'A query for whether we are inside a trigger';
    }
}
