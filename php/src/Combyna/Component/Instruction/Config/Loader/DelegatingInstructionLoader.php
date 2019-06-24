<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Instruction\Config\Loader;

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Instruction\Config\Act\UnknownInstructionNode;

/**
 * Class DelegatingInstructionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingInstructionLoader implements InstructionLoaderInterface, DelegatorInterface
{
    /**
     * @var InstructionTypeLoaderInterface[]
     */
    private $loaders = [];

    /**
     * @param InstructionTypeLoaderInterface $instructionTypeLoader
     */
    public function addLoader(InstructionTypeLoaderInterface $instructionTypeLoader)
    {
        $this->loaders[$instructionTypeLoader->getType()] = $instructionTypeLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $instructionConfig)
    {
        if (!array_key_exists('type', $instructionConfig)) {
            // Missing "type" element
            return new UnknownInstructionNode('Instruction has no "type" specified');
        }

        $type = $instructionConfig['type'];

        if (!array_key_exists($type, $this->loaders)) {
            // No loader is registered for instructions of this type
            return new UnknownInstructionNode(sprintf(
                'Instruction is of unknown type "%s"',
                $type
            ));
        }

        // Remove the "type" element as the instruction type loader won't be expecting it
        unset($instructionConfig['type']);

        return $this->loaders[$type]->load($instructionConfig);
    }
}
