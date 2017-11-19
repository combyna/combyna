<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity\Instruction;

use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Interface EntityInstructionInterface
 *
 * Performs an operation on an entity
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SetEntityAttributeInstruction implements EntityInstructionInterface
{
    /**
     * {@inheritdoc}
     */
    public function perform(
        EvaluationContextInterface $evaluationContext,
        ProgramStateInterface $programState
    ) {
        $entityState = $evaluationContext->getEntityState();

        // TODO: Figure out how to create the new app state with this new entity state (?)

        return $entityState->withNewAttributeValue();
    }
}
