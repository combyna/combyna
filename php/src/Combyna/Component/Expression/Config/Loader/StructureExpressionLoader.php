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
use Combyna\Component\Config\Parameter\Type\ExpressionBagParameterType;
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Expression\Config\Act\StructureExpressionNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Expression\StructureExpression;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

/**
 * Class StructureExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StructureExpressionLoader implements ExpressionTypeLoaderInterface
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
                new NamedParameter('attributes', new ExpressionBagParameterType('attribute bag'))
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnknownExpressionNode(
                'Invalid structure: ' . $exception->getMessage(),
                new NullActNodeAdopter()
            );
        }

        $attributeExpressionBagNode = $parsedArgumentBag->getNamedExpressionBagArgument('attributes');

        return new StructureExpressionNode($attributeExpressionBagNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return StructureExpression::TYPE;
    }
}
