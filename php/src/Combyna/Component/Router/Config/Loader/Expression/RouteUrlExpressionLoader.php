<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Loader\Expression;

use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Loader\ArgumentParserInterface;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\OptionalParameter;
use Combyna\Component\Config\Parameter\Type\ExpressionParameterType;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Router\Config\Act\Expression\RouteUrlExpressionNode;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

/**
 * Class RouteUrlExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteUrlExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'route_url';

    /**
     * @var ArgumentParserInterface
     */
    private $argumentParser;

    /**
     * @param ArgumentParserInterface $argumentParser
     */
    public function __construct(ArgumentParserInterface $argumentParser)
    {
        $this->argumentParser = $argumentParser;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        try {
            $parsedArgumentBag = $this->argumentParser->parseArguments($config, [
                new NamedParameter('route', new ExpressionParameterType('route name expression')),
                new OptionalParameter(
                    new NamedParameter('arguments', new ExpressionParameterType('route arguments structure'))
                )
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnknownExpressionNode($exception->getMessage(), new NullActNodeAdopter());
        }

        $routeNameExpressionNode = $parsedArgumentBag->getNamedExpressionArgument('route');
        $argumentStructureExpressionNode = $parsedArgumentBag->getNamedExpressionArgument('arguments');

        return new RouteUrlExpressionNode($routeNameExpressionNode, $argumentStructureExpressionNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
