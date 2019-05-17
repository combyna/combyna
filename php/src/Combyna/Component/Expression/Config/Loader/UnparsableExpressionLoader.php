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
use Combyna\Component\Config\Loader\ConfigParserInterface;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\ExpressionLanguage\Config\Act\UnparsableExpressionNode;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

/**
 * Class UnparsableExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnparsableExpressionLoader implements ExpressionTypeLoaderInterface
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
                new NamedParameter('type', new StringParameterType('expression type')),
                new NamedParameter('expression', new StringParameterType('expression string'))
            ]);
        } catch (ArgumentParseException $exception) {
            // The unparsable expression's config is in an invalid format
            return new UnknownExpressionNode(
                'Unparsable expression in invalid format - ' . $exception->getMessage(),
                new NullActNodeAdopter()
            );
        }

        return new UnparsableExpressionNode(
            $parsedArgumentBag->getNamedStringArgument('expression')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return UnparsableExpressionNode::TYPE;
    }
}
