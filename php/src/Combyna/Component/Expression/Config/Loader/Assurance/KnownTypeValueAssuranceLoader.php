<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader\Assurance;

use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Loader\ArgumentParser;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\TextParameterType;
use Combyna\Component\Expression\Assurance\KnownTypeValueAssurance;
use Combyna\Component\Expression\Config\Act\Assurance\KnownTypeValueAssuranceNode;
use Combyna\Component\Expression\Config\Act\Assurance\UnknownAssuranceNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\Config\Loader\TypeLoaderInterface;

/**
 * Class KnownTypeValueAssuranceLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class KnownTypeValueAssuranceLoader implements AssuranceTypeLoaderInterface
{
    /**
     * @var ArgumentParser
     */
    private $argumentParser;

    /**
     * @var TypeLoaderInterface
     */
    private $typeLoader;

    /**
     * @param ArgumentParser $argumentParser
     * @param TypeLoaderInterface $typeLoader
     */
    public function __construct(ArgumentParser $argumentParser, TypeLoaderInterface $typeLoader)
    {
        $this->argumentParser = $argumentParser;
        $this->typeLoader = $typeLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint()
    {
        return KnownTypeValueAssurance::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function load(
        $assuredStaticName,
        $constraintName,
        ExpressionNodeInterface $expressionNode,
        array $extra
    ) {
        try {
            $parsedArgumentBag = $this->argumentParser->parseArguments([
                ArgumentParser::NAMED_ARGUMENTS => $extra
            ], [
                new NamedParameter('type', new TextParameterType('type name'))
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnknownAssuranceNode($assuredStaticName, $constraintName, $exception->getMessage());
        }

        $typeName = $parsedArgumentBag->getNamedTextArgument('type');
        $typeDeterminer = $this->typeLoader->load($typeName);

        return new KnownTypeValueAssuranceNode(
            $expressionNode,
            $assuredStaticName,
            $typeDeterminer
        );
    }
}
