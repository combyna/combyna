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
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Ui\Store\Validation\Constraint\ViewStoreHasSlotConstraint;
use Combyna\Component\Ui\Store\Validation\Query\ViewStoreSlotTypeQuery;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class SetViewStoreSlotInstructionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SetViewStoreSlotInstructionNode extends AbstractActNode implements ViewStoreInstructionNodeInterface
{
    const TYPE = 'set_slot';

    /**
     * @var string
     */
    private $slotName;

    /**
     * @var ExpressionNodeInterface
     */
    private $valueExpressionNode;

    /**
     * @param string $slotName
     * @param ExpressionNodeInterface $valueExpressionNode
     */
    public function __construct($slotName, ExpressionNodeInterface $valueExpressionNode)
    {
        $this->slotName = $slotName;
        $this->valueExpressionNode = $valueExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(new ViewStoreHasSlotConstraint($this->slotName));

        $specBuilder->addChildNode($this->valueExpressionNode);

        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->valueExpressionNode,
                new QueriedResultTypeDeterminer(
                    new ViewStoreSlotTypeQuery($this->slotName),
                    $this
                ),
                'value expression result type must match slot type'
            )
        );
    }

    /**
     * Fetches the name of the slot in the view store that this instruction will update
     *
     * @return string
     */
    public function getSlotName()
    {
        return $this->slotName;
    }

    /**
     * Fetches the expression that will be evaluated to give the new static value of the store slot
     *
     * @return ExpressionNodeInterface
     */
    public function getValueExpression()
    {
        return $this->valueExpressionNode;
    }
}
