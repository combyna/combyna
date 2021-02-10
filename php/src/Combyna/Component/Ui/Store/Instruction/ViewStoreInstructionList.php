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
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;

/**
 * Class ViewStoreInstructionList
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreInstructionList implements ViewStoreInstructionListInterface
{
    /**
     * @var ViewStoreInstructionInterface[]
     */
    private $instructions;

    /**
     * @param ViewStoreInstructionInterface[] $instructions
     */
    public function __construct(array $instructions)
    {
        $this->instructions = $instructions;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * {@inheritdoc}
     */
    public function performAll(
        EvaluationContextInterface $evaluationContext,
        ViewStoreStateInterface $viewStoreState
    ) {
        // TODO: Look into whether we need to create a new evaluation context
        //       if a new store state is returned (state objects are immutable)
        //       - test with a set-slot instruction followed by another instruction
        //         that reads that same slot
        foreach ($this->instructions as $instruction) {
            $viewStoreState = $instruction->perform($evaluationContext, $viewStoreState);
        }

        return $viewStoreState;
    }
}
