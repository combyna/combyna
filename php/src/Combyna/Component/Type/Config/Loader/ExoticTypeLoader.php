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
use Combyna\Component\Config\Parameter\ExtraParameter;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Type\Exotic\ExoticTypeDeterminerFactoryInterface;
use Combyna\Component\Type\Validation\Type\ExoticTypeDeterminer;
use Combyna\Component\Validator\Type\UnresolvedTypeDeterminer;

/**
 * Class ExoticTypeLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExoticTypeLoader implements TypeTypeLoaderInterface
{
    /**
     * @var ConfigParserInterface
     */
    private $configParser;

    /**
     * @var ExoticTypeDeterminerFactoryInterface
     */
    private $exoticTypeDeterminerFactory;

    /**
     * @param ExoticTypeDeterminerFactoryInterface $exoticTypeDeterminerFactory
     * @param ConfigParserInterface $configParser
     */
    public function __construct(
        ExoticTypeDeterminerFactoryInterface $exoticTypeDeterminerFactory,
        ConfigParserInterface $configParser
    ) {
        $this->configParser = $configParser;
        $this->exoticTypeDeterminerFactory = $exoticTypeDeterminerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        try {
            $parsedArgumentBag = $this->configParser->parseArguments($config, [
                new NamedParameter('type', new StringParameterType('type')),
                new NamedParameter('name', new StringParameterType('exotic type determiner name')),
                new ExtraParameter()
            ]);
        } catch (ArgumentParseException $exception) {
            return new UnresolvedTypeDeterminer('Invalid exotic type: ' . $exception->getMessage());
        }

        $exoticTypeDeterminerName = $parsedArgumentBag->getNamedStringArgument('name');
        $exoticConfig = $parsedArgumentBag->getExtraArguments();

        return new ExoticTypeDeterminer(
            $this->exoticTypeDeterminerFactory,
            $exoticTypeDeterminerName,
            $exoticConfig
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return ['exotic'];
    }
}
