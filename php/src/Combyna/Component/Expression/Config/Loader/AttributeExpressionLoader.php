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

use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\ExpressionParameterType;
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Expression\AttributeExpression;
use Combyna\Component\Expression\Config\Act\AttributeExpressionNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

/**
 * Class AttributeExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AttributeExpressionLoader implements ExpressionTypeLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @param ConfigParser $configParser
     */
    public function __construct(ConfigParser $configParser)
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
                new NamedParameter('type', new StringParameterType('expression type')),
                new NamedParameter('structure', new ExpressionParameterType('structure')),
                new NamedParameter('attribute', new StringParameterType('attribute name'))
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnknownExpressionNode(
                'Invalid attribute fetch: ' . $exception->getMessage(),
                new NullActNodeAdopter()
            );
        }

        $structureExpressionNode = $parsedArgumentBag->getNamedExpressionArgument('structure');
        $attributeName = $parsedArgumentBag->getNamedStringArgument('attribute');

        return new AttributeExpressionNode($structureExpressionNode, $attributeName);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return AttributeExpression::TYPE;
    }
}
