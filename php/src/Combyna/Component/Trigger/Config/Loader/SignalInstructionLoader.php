<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Config\Loader;

use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Instruction\Config\Loader\InstructionTypeLoaderInterface;
use Combyna\Component\Trigger\Config\Act\SignalInstructionNode;
use InvalidArgumentException;

/**
 * Class SignalInstructionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalInstructionLoader implements InstructionTypeLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @param ConfigParser $configParser
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     */
    public function __construct(
        ConfigParser $configParser,
        ExpressionBagLoaderInterface $expressionBagLoader
    ) {
        $this->configParser = $configParser;
        $this->expressionBagLoader = $expressionBagLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return SignalInstructionNode::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $instructionConfig)
    {
        $signalReference = $this->configParser->getElement(
            $instructionConfig,
            'signal',
            'signal library and name'
        );
        $signalParts = explode('.', $signalReference, 2);

        if (count($signalParts) < 2) {
            throw new InvalidArgumentException(
                'Signal name must be in format <library>.<name>, received "' . $signalReference . '"'
            );
        }

        list($signalLibraryName, $signalName) = $signalParts;

        $payloadExpressionBagConfig = $this->configParser->getOptionalElement(
            $instructionConfig,
            'payload',
            'signal payload expressions',
            [],
            'array'
        );
        $payloadExpressionBagNode = $this->expressionBagLoader->load($payloadExpressionBagConfig);

        return new SignalInstructionNode($signalLibraryName, $signalName, $payloadExpressionBagNode);
    }
}
