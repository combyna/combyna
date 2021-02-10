<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Store\Config\Loader;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Loader\ConfigParserInterface;
use Combyna\Component\Config\Parameter\CallbackOptionalParameter;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\ExpressionParameterType;
use Combyna\Component\Config\Parameter\Type\FixedStaticBagModelParameterType;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Store\Config\Act\QueryNode;
use Combyna\Component\Store\Config\Act\UnknownQueryNode;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

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
     * @var ConfigParserInterface
     */
    private $configParser;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ConfigParserInterface $configParser
     * @param ExpressionLoaderInterface $expressionLoader
     * @param FixedStaticBagModelLoaderInterface $bagModelLoader
     */
    public function __construct(
        ConfigParserInterface $configParser,
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
        try {
            $parsedArgumentBag = $this->configParser->parseArguments($config, [
                new CallbackOptionalParameter(
                    new NamedParameter('parameters', new FixedStaticBagModelParameterType('query parameters')),
                    function () {
                        return new FixedStaticBagModelNode([]);
                    }
                ),
                new NamedParameter('expression', new ExpressionParameterType('expression to evaluate for the query'))
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnknownQueryNode($exception->getMessage(), new NullActNodeAdopter());
        }

        $parameterBagModel = $parsedArgumentBag->getNamedFixedStaticBagModelArgument('parameters');
        $expressionNode = $parsedArgumentBag->getNamedExpressionArgument('expression');

        return new QueryNode($name, $parameterBagModel, $expressionNode);
    }
}
