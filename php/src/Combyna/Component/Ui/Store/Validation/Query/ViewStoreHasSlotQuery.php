<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class ViewStoreHasSlotQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreHasSlotQuery implements BooleanQueryInterface
{
    /**
     * @var string
     */
    private $slotName;

    /**
     * @param string $slotName
     */
    public function __construct($slotName)
    {
        $this->slotName = $slotName;
    }

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
        return 'Whether the current view\'s store has a slot called "' . $this->slotName . '"';
    }

    /**
     * Fetches the name of the slot to query the existence of
     *
     * @return string
     */
    public function getSlotName()
    {
        return $this->slotName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
