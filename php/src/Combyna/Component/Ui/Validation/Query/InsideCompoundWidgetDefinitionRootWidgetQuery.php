<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class InsideCompoundWidgetDefinitionRootWidgetQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InsideCompoundWidgetDefinitionRootWidgetQuery implements BooleanQueryInterface
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
        return 'Whether we are inside a compound widget definition\'s root widget';
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
