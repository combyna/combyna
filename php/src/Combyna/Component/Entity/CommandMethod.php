<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Entity\Instruction\EntityInstructionInterface;

/**
 * Class CommandMethod
 *
 * Performs a sequence of instructions that operate on an entity
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CommandMethod implements CommandMethodInterface
{
    /**
     * @var EntityInstructionInterface[]
     */
    private $instructions;

    /**
     * @var string
     */
    private $name;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $parameterBagModel;

    /**
     * @param string $name
     * @param FixedStaticBagModelInterface $parameterBagModel
     * @param EntityInstructionInterface[] $instructions
     */
    public function __construct($name, FixedStaticBagModelInterface $parameterBagModel, array $instructions)
    {
        $this->instructions = $instructions;
        $this->name = $name;
        $this->parameterBagModel = $parameterBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function perform(StaticBagInterface $argumentStaticBag, EntityStorageInterface $storage)
    {
        $this->parameterBagModel->assertValidArgumentBag($argumentStaticBag);

        // FIXME: Should take app state as an arg and return the new one, etc.

        $commandEvaluationContext = $evaluationContext->createSubCommandEvaluationContext($argumentStaticBag);

        foreach ($this->instructions as $instruction) {
            $instruction->perform($commandEvaluationContext, $programState);
        }
    }
}
