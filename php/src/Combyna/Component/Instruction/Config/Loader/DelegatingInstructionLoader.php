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

use Combyna\Component\Common\DelegatorInterface;
use InvalidArgumentException;

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
            throw new InvalidArgumentException('Missing "type" element');
        }

        $type = $instructionConfig['type'];

        if (!array_key_exists($type, $this->loaders)) {
            throw new InvalidArgumentException(
                'No loader is registered for instructions of type "' . $type . '"'
            );
        }

        return $this->loaders[$type]->load($instructionConfig);
    }
}
