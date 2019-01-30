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

use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Loader\ArgumentParser;
use Combyna\Component\Config\Parameter\ExtraParameter;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\ExpressionParameterType;
use Combyna\Component\Config\Parameter\Type\TextParameterType;
use Combyna\Component\Expression\Config\Act\GuardExpressionNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Expression\Config\Loader\Assurance\AssuranceLoaderInterface;

/**
 * Class GuardExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GuardExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'guard';

    /**
     * @var ArgumentParser
     */
    private $argumentParser;

    /**
     * @var AssuranceLoaderInterface
     */
    private $assuranceLoader;

    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ArgumentParser $argumentParser
     * @param ExpressionLoaderInterface $expressionLoader
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     * @param AssuranceLoaderInterface $assuranceLoader
     */
    public function __construct(
        ArgumentParser $argumentParser,
        ExpressionLoaderInterface $expressionLoader,
        ExpressionBagLoaderInterface $expressionBagLoader,
        AssuranceLoaderInterface $assuranceLoader
    ) {
        $this->argumentParser = $argumentParser;
        $this->assuranceLoader = $assuranceLoader;
        $this->expressionBagLoader = $expressionBagLoader;
        $this->expressionLoader = $expressionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        try {
            $parsedArgumentBag = $this->argumentParser->parseArguments($config, [
                new NamedParameter('name', new TextParameterType('assured static name')),
                new NamedParameter('constraint', new TextParameterType('type of constraint to assure')),
                new NamedParameter('expression', new ExpressionParameterType('expression to test against assurance')),
                new NamedParameter('then', new ExpressionParameterType('consequent expression, if assurance is met')),
                new NamedParameter('else', new ExpressionParameterType('alternate expression, if assurance is not met')),
                new ExtraParameter()
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnknownExpressionNode($exception->getMessage());
        }

        $assuredStaticName = $parsedArgumentBag->getNamedTextArgument('name');
        $constraintName = $parsedArgumentBag->getNamedTextArgument('constraint');
        $expressionNode = $parsedArgumentBag->getNamedExpressionArgument('expression');
        $consequentExpressionNode = $parsedArgumentBag->getNamedExpressionArgument('then');
        $alternateExpressionNode = $parsedArgumentBag->getNamedExpressionArgument('else');
        $extra = $parsedArgumentBag->getExtraArguments();

        $assuranceNode = $this->assuranceLoader->load(
            $assuredStaticName,
            $constraintName,
            $expressionNode,
            $extra
        );

        return new GuardExpressionNode(
            [$assuranceNode],
            $consequentExpressionNode,
            $alternateExpressionNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
