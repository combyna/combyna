<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Behaviour\Query\Specifier;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;

/**
 * Class InsideViewStoreQuerySpecifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InsideViewStoreQuerySpecifier implements QuerySpecifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'A query for whether we are inside a view store';
    }
}
