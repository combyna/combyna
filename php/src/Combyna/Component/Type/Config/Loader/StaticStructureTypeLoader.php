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
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\FixedStaticBagModelParameterType;
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Validator\Type\StaticStructureTypeDeterminer;
use Combyna\Component\Validator\Type\UnresolvedTypeDeterminer;

/**
 * Class StaticStructureTypeLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticStructureTypeLoader implements TypeTypeLoaderInterface
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
                new NamedParameter('attributes', new FixedStaticBagModelParameterType('attributes model'))
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnresolvedTypeDeterminer('Invalid structure: ' . $exception->getMessage());
        }

        $attributeBagModelNode = $parsedArgumentBag->getNamedFixedStaticBagModelArgument('attributes');

        return new StaticStructureTypeDeterminer($attributeBagModelNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return ['structure'];
    }
}
