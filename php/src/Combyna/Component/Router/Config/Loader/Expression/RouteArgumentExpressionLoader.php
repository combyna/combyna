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
use Combyna\Component\Config\Parameter\PositionalParameter;
use Combyna\Component\Config\Parameter\Type\TextParameterType;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Router\Config\Act\Expression\RouteArgumentExpressionNode;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

/**
 * Class RouteArgumentExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteArgumentExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'route_arg';

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
                new PositionalParameter('name', new TextParameterType('route name'))
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnknownExpressionNode($exception->getMessage(), new NullActNodeAdopter());
        }

        $routeArgumentName = $parsedArgumentBag->getNamedExpressionArgument('name');

        return new RouteArgumentExpressionNode($routeArgumentName);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
