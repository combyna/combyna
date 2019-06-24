<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Config\Loader;

use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Loader\ConfigParserInterface;
use Combyna\Component\Config\Parameter\CallbackOptionalParameter;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\ExpressionParameterType;
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Config\Parameter\Type\TypeParameterType;
use Combyna\Component\Validator\Type\AnyTypeDeterminer;
use Combyna\Component\Validator\Type\UnresolvedTypeDeterminer;
use Combyna\Component\Validator\Type\ValuedTypeDeterminer;

/**
 * Class ValuedTypeLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValuedTypeLoader implements TypeTypeLoaderInterface
{
    /**
     * @var ConfigParserInterface
     */
    private $configParser;

    /**
     * @param ConfigParserInterface $configParser
     */
    public function __construct(ConfigParserInterface $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        try {
            $parsedArgumentBag = $this->configParser->parseArguments($config, [
                new NamedParameter('type', new StringParameterType('type')),
                new CallbackOptionalParameter(
                    new NamedParameter('wraps', new TypeParameterType('wrapped type')),
                    function () {
                        return new AnyTypeDeterminer();
                    }
                ),
                new NamedParameter('value', new ExpressionParameterType('value expression')),
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnresolvedTypeDeterminer('Invalid valued type: ' . $exception->getMessage());
        }

        $wrappedTypeDeterminer = $parsedArgumentBag->getNamedTypeArgument('wraps');
        $valueExpressionNode = $parsedArgumentBag->getNamedExpressionArgument('value');

        return new ValuedTypeDeterminer($wrappedTypeDeterminer, $valueExpressionNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return ['valued'];
    }
}
