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
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\ExpressionParameterType;
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\Config\Act\BinaryArithmeticExpressionNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

/**
 * Class BinaryArithmeticExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BinaryArithmeticExpressionLoader implements ExpressionTypeLoaderInterface
{
    /**
     * @var ExpressionConfigParserInterface
     */
    private $configParser;

    /**
     * @param ExpressionConfigParserInterface $configParser
     */
    public function __construct(ExpressionConfigParserInterface $configParser)
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
                new NamedParameter('left', new ExpressionParameterType('left operand')),
                new NamedParameter('operator', new StringParameterType('operator')),
                new NamedParameter('right', new ExpressionParameterType('right operand'))
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnknownExpressionNode($exception->getMessage(), new NullActNodeAdopter());
        }

        return new BinaryArithmeticExpressionNode(
            $parsedArgumentBag->getNamedExpressionArgument('left'),
            $parsedArgumentBag->getNamedStringArgument('operator'),
            $parsedArgumentBag->getNamedExpressionArgument('right')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return BinaryArithmeticExpression::TYPE;
    }
}
