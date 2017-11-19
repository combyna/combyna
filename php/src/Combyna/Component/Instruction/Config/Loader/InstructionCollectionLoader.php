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

/**
 * Class InstructionCollectionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InstructionCollectionLoader implements InstructionCollectionLoaderInterface
{
    /**
     * @var InstructionLoaderInterface
     */
    private $instructionLoader;

    /**
     * @param InstructionLoaderInterface $instructionLoader
     */
    public function __construct(InstructionLoaderInterface $instructionLoader)
    {
        $this->instructionLoader = $instructionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadCollection(array $config)
    {
        $instructionNodes = [];

        foreach ($config as $instructionConfig) {
            $instructionNodes[] = $this->instructionLoader->load($instructionConfig);
        }

        return $instructionNodes;
    }
}
