<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Instruction\Config\Loader\InstructionTypeLoaderInterface;
use Combyna\Component\Ui\Store\Config\Act\SetViewStoreSlotInstructionNode;

/**
 * Class SetViewStoreSlotInstructionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SetViewStoreSlotInstructionLoader implements InstructionTypeLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ConfigParser $configParser
     * @param ExpressionLoaderInterface $expressionLoader
     */
    public function __construct(
        ConfigParser $configParser,
        ExpressionLoaderInterface $expressionLoader
    ) {
        $this->configParser = $configParser;
        $this->expressionLoader = $expressionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return SetViewStoreSlotInstructionNode::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $instructionConfig)
    {
        $slotName = $this->configParser->getElement(
            $instructionConfig,
            'slot',
            'view store slot name'
        );
        $valueExpressionConfig = $this->configParser->getElement(
            $instructionConfig,
            'value',
            'value expression',
            ['array', 'string']
        );
        $valueExpressionNode = $this->expressionLoader->load($valueExpressionConfig);

        return new SetViewStoreSlotInstructionNode($slotName, $valueExpressionNode);
    }
}
