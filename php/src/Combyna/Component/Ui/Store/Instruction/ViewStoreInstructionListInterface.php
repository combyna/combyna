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
use Combyna\Component\Instruction\InstructionListInterface as BaseInstructionListInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;

/**
 * Interface ViewStoreInstructionListInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewStoreInstructionListInterface extends BaseInstructionListInterface
{
    /**
     * {@inheritdoc}
     *
     * @return ViewStoreInstructionInterface[]
     */
    public function getInstructions();

    /**
     * Performs all instructions in this list in order, returning the final app state
     *
     * @param EvaluationContextInterface $evaluationContext
     * @param ViewStoreStateInterface $viewStoreState
     * @return ViewStoreStateInterface
     */
    public function performAll(
        EvaluationContextInterface $evaluationContext,
        ViewStoreStateInterface $viewStoreState
    );
}
