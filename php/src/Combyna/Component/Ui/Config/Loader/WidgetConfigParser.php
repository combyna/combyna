<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParserInterface;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\OptionalParameter;
use Combyna\Component\Config\Parameter\Type\ArrayParameterType;
use Combyna\Component\Config\Parameter\Type\StringParameterType;

/**
 * Class WidgetConfigParser
 *
 * Encapsulates parsing data from a config array (eg. from a YAML config file) for a widget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetConfigParser implements WidgetConfigParserInterface
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
    public function parseArguments(array $config, array $parameterList = [])
    {
        return $this->configParser->parseArguments(
            $config,
            array_merge(
                [
                    new NamedParameter('type', new StringParameterType('widget type')),
                    new OptionalParameter(
                        new NamedParameter(
                            'captures',
                            new ArrayParameterType('captures')
                        )
                    ),
                    new OptionalParameter(
                        new NamedParameter(
                            'tags',
                            new ArrayParameterType('tags')
                        )
                    )
                ],
                $parameterList
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($value)
    {
        return $this->configParser->toArray($value);
    }
}
