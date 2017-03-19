<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity;

use Combyna\Component\Entity\Instruction\EntityInstructionInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Parameter\ParameterBagModelInterface;

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
     * @var ParameterBagModelInterface
     */
    private $parameterBagModel;

    /**
     * @param string $name
     * @param ParameterBagModelInterface $parameterBagModel
     * @param EntityInstructionInterface[] $instructions
     */
    public function __construct($name, ParameterBagModelInterface $parameterBagModel, array $instructions)
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

        foreach ($this->instructions as $instruction) {
            $instruction->perform($argumentStaticBag, $storage);
        }
    }
}
