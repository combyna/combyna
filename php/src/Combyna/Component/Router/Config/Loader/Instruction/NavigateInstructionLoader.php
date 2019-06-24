<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Loader\Instruction;

use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Loader\ConfigParserInterface;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\OptionalParameter;
use Combyna\Component\Config\Parameter\Type\ExpressionParameterType;
use Combyna\Component\Instruction\Config\Act\InstructionNodeInterface;
use Combyna\Component\Instruction\Config\Act\UnknownInstructionNode;
use Combyna\Component\Instruction\Config\Loader\InstructionTypeLoaderInterface;
use Combyna\Component\Router\Config\Act\Instruction\NavigateInstructionNode;

/**
 * Class NavigateInstructionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NavigateInstructionLoader implements InstructionTypeLoaderInterface
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
    public function getType()
    {
        return NavigateInstructionNode::TYPE;
    }

    /**
     * Creates a navigate instruction node from a config array
     *
     * @param array $instructionConfig
     * @return InstructionNodeInterface
     */
    public function load(array $instructionConfig)
    {
        try {
            $parsedArgumentBag = $this->configParser->parseArguments($instructionConfig, [
                new NamedParameter('route', new ExpressionParameterType('route name expression')),
                new OptionalParameter(
                    new NamedParameter('arguments', new ExpressionParameterType('route arguments structure'))
                )
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnknownInstructionNode($exception->getMessage());
        }

        $routeNameExpressionNode = $parsedArgumentBag->getNamedExpressionArgument('route');
        $routeArgumentStructureExpressionNode = $parsedArgumentBag->getNamedExpressionArgument('arguments');

        return new NavigateInstructionNode(
            $routeNameExpressionNode,
            $routeArgumentStructureExpressionNode
        );
    }
}
