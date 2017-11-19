<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Expression;

use Combyna\Component\Expression\AbstractExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class SlotExpression
 *
 * Returns the static value of a slot in the current UI store
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SlotExpression extends AbstractExpression
{
    const TYPE = 'slot';

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
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        return $evaluationContext->getStoreSlotStatic($this->slotName);
    }
}
