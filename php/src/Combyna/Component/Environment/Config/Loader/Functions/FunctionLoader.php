<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Loader\Functions;

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Environment\Config\Act\NativeFunctionNode;
use Combyna\Component\Environment\Config\Act\UnknownFunctionTypeNode;
use Combyna\Component\Type\Config\Loader\TypeLoaderInterface;

/**
 * Class FunctionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionLoader implements FunctionLoaderInterface
{
    /**
     * @var FixedStaticBagModelLoaderInterface
     */
    private $bagModelLoader;

    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var TypeLoaderInterface
     */
    private $typeLoader;

    /**
     * @param ConfigParser $configParser
     * @param FixedStaticBagModelLoaderInterface $bagModelLoader
     * @param TypeLoaderInterface $typeLoader
     */
    public function __construct(
        ConfigParser $configParser,
        FixedStaticBagModelLoaderInterface $bagModelLoader,
        TypeLoaderInterface $typeLoader
    ) {
        $this->bagModelLoader = $bagModelLoader;
        $this->configParser = $configParser;
        $this->typeLoader = $typeLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($libraryName, $functionName, array $functionConfig)
    {
        $functionType = $this->configParser->getElement($functionConfig, 'type', 'function type');
        $parameterBagModelConfig = $this->configParser->getElement(
            $functionConfig,
            'parameters',
            'parameter bag',
            'array'
        );
        $returnType = $this->configParser->getElement(
            $functionConfig,
            'return',
            'function return type',
            ['array', 'string'] // Type could just be a string or be an array for the expanded variant
        );

        if ($functionType !== 'native') {
            return new UnknownFunctionTypeNode($libraryName, $functionName, $functionType);
        }

        return new NativeFunctionNode(
            $libraryName,
            $functionName,
            $this->bagModelLoader->load($parameterBagModelConfig),
            $this->typeLoader->load($returnType)
        );
    }
}
