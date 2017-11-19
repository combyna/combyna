<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Instruction\Config\Loader\InstructionCollectionLoaderInterface;
use Combyna\Component\Signal\Config\Act\SignalDefinitionReferenceNode;
use Combyna\Component\Signal\Config\Act\SignalHandlerNode;

/**
 * Class SignalHandlerLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalHandlerLoader implements SignalHandlerLoaderInterface
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
     * @var InstructionCollectionLoaderInterface
     */
    private $instructionCollectionLoader;

    /**
     * @var SignalDefinitionReferenceLoaderInterface
     */
    private $signalDefinitionReferenceLoader;

    /**
     * @param ConfigParser $configParser
     * @param SignalDefinitionReferenceLoaderInterface $signalDefinitionReferenceLoader
     * @param InstructionCollectionLoaderInterface $instructionCollectionLoader
     * @param ExpressionLoaderInterface $expressionLoader
     */
    public function __construct(
        ConfigParser $configParser,
        SignalDefinitionReferenceLoaderInterface $signalDefinitionReferenceLoader,
        InstructionCollectionLoaderInterface $instructionCollectionLoader,
        ExpressionLoaderInterface $expressionLoader
    ) {
        $this->configParser = $configParser;
        $this->expressionLoader = $expressionLoader;
        $this->instructionCollectionLoader = $instructionCollectionLoader;
        $this->signalDefinitionReferenceLoader = $signalDefinitionReferenceLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(
        $signalLibraryName,
        $signalName,
        array $signalHandlerConfig
    ) {
        $signalDefinitionReferenceNode = new SignalDefinitionReferenceNode($signalLibraryName, $signalName);
        $instructionConfig = $this->configParser->getOptionalElement(
            $signalHandlerConfig,
            'instructions',
            'signal handler instructions',
            [],
            'array'
        );
        $guardExpressionConfig = $this->configParser->getOptionalElement(
            $signalHandlerConfig,
            'guard',
            'signal handler guard expression',
            [],
            'array'
        );

        $instructionNodes = $this->instructionCollectionLoader->loadCollection($instructionConfig);
        $guardExpressionNode = $guardExpressionConfig !== [] ?
            $this->expressionLoader->load($guardExpressionConfig) :
            null;

        return new SignalHandlerNode(
            $signalDefinitionReferenceNode,
            $instructionNodes,
            $guardExpressionNode
        );
    }
}
