<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Store\Config\Loader;

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Store\Config\Act\QueryNode;

/**
 * Class QueryLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class QueryLoader implements QueryLoaderInterface
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
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ConfigParser $configParser
     * @param ExpressionLoaderInterface $expressionLoader
     * @param FixedStaticBagModelLoaderInterface $bagModelLoader
     */
    public function __construct(
        ConfigParser $configParser,
        ExpressionLoaderInterface $expressionLoader,
        FixedStaticBagModelLoaderInterface $bagModelLoader
    ) {
        $this->bagModelLoader = $bagModelLoader;
        $this->configParser = $configParser;
        $this->expressionLoader = $expressionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($name, array $config)
    {
        $parameterBagModelConfig = $this->configParser->getElement(
            $config,
            'parameters',
            'query parameter model',
            'array'
        );
        $expressionConfig = $this->configParser->getElement(
            $config,
            'expression',
            'query expression',
            'array'
        );

        return new QueryNode(
            $name,
            $this->bagModelLoader->load($parameterBagModelConfig),
            $this->expressionLoader->load($expressionConfig)
        );
    }
}
