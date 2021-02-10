<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParserInterface;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Expression\Config\Act\StaticNodeInterface;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;
use InvalidArgumentException;

/**
 * Class ExpressionConfigParser
 *
 * Encapsulates parsing data from a config array (eg. from a YAML config file)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionConfigParser implements ExpressionConfigParserInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ConfigParserInterface
     */
    private $configParser;

    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ConfigParserInterface $configParser
     * @param ExpressionLoaderInterface $expressionLoader
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     * @param BagFactoryInterface $bagFactory
     */
    public function __construct(
        ConfigParserInterface $configParser,
        ExpressionLoaderInterface $expressionLoader,
        ExpressionBagLoaderInterface $expressionBagLoader,
        BagFactoryInterface $bagFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->configParser = $configParser;
        $this->expressionBagLoader = $expressionBagLoader;
        $this->expressionLoader = $expressionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedArgumentStaticBag(array $config)
    {
        $namedArgumentConfig = $config[self::NAMED_ARGUMENTS];

        return $this->expressionBagLoader->load($namedArgumentConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function getPositionalArgument(array $config, $position, $context)
    {
        $positionalArgumentConfig = $config[self::POSITIONAL_ARGUMENTS];

        if (!array_key_exists($position, $positionalArgumentConfig)) {
            return new UnknownExpressionNode(
                'Missing required argument at position #' . $position . ' for ' . $context,
                new NullActNodeAdopter()
            );
        }

        return $this->expressionLoader->load($positionalArgumentConfig[$position]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPositionalArgumentNative(array $config, $position, $requiredStaticType, $context)
    {
        $expressionNode = $this->getPositionalArgument($config, $position, $context);

        if (!$expressionNode instanceof StaticNodeInterface) {
            throw new InvalidArgumentException(
                'Argument at position #' . $position . ' for ' . $context .
                ' must be of a static expression type but was of "' . $expressionNode->getType() . '"'
            );
        }

        if ($expressionNode->getType() !== $requiredStaticType) {
            throw new InvalidArgumentException(
                'Argument at position #' . $position . ' for ' . $context .
                ' must be of type "' . $requiredStaticType . '" but was of "' . $expressionNode->getType() . '"'
            );
        }

        return $expressionNode->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function parseArguments(array $config, array $parameterList)
    {
        return $this->configParser->parseArguments(
            $config,
            array_merge(
                [
                    // All expressions must provide their type name
                    new NamedParameter('type', new StringParameterType('expression type name'))
                ],
                $parameterList
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($value)
    {
        return $this->configParser->toArray($value);
    }
}
