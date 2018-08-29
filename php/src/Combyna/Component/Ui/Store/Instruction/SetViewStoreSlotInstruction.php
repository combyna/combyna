<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Instruction;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;

/**
 * Class SetViewStoreSlotInstruction
 *
 * An instruction for only view store signal handlers to use that updates the value of a store slot
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SetViewStoreSlotInstruction implements ViewStoreInstructionInterface
{
    /**
     * @var string
     */
    private $slotName;

    /**
     * @var ExpressionInterface
     */
    private $valueExpression;

    /**
     * @param string $slotName
     * @param ExpressionInterface $valueExpression
     */
    public function __construct(
        $slotName,
        ExpressionInterface $valueExpression
    ) {
        $this->slotName = $slotName;
        $this->valueExpression = $valueExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function perform(
        EvaluationContextInterface $evaluationContext,
        ViewStoreStateInterface $viewStoreState
    ) {
        $newSlotStatic = $this->valueExpression->toStatic($evaluationContext);

        return $viewStoreState->withSlotStatic($this->slotName, $newSlotStatic);
    }
}
