<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\Config\Act\AbstractExpressionNode;
use Combyna\Component\Ui\Store\Expression\SlotExpression;
use Combyna\Component\Ui\Store\Validation\Constraint\InsideViewStoreConstraint;
use Combyna\Component\Ui\Store\Validation\Constraint\ViewStoreHasSlotConstraint;
use Combyna\Component\Ui\Store\Validation\Query\ViewStoreSlotTypeQuery;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class ViewStoreSlotExpressionNode
 *
 * Fetches the value of a store slot (only accessible from its queries and command/signal handler instructions)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreSlotExpressionNode extends AbstractExpressionNode
{
    const TYPE = SlotExpression::TYPE;

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
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        // View store slots may only be accessed directly from inside the store itself,
        // not from anywhere else inside the view. To access from elsewhere in the view
        // (eg. from a widget attribute expression), use a query on the view store
        $specBuilder->addConstraint(new InsideViewStoreConstraint());
        $specBuilder->addConstraint(new ViewStoreHasSlotConstraint($this->slotName));
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new QueriedResultTypeDeterminer(new ViewStoreSlotTypeQuery($this->slotName), $this);
    }

    /**
     * Fetches the name of the slot to be fetched
     *
     * @return string
     */
    public function getSlotName()
    {
        return $this->slotName;
    }
}
