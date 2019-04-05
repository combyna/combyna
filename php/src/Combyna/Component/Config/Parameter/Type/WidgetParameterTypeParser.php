<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Parameter\Type;

use Combyna\Component\Ui\Config\Act\WidgetNodeInterface;
use Combyna\Component\Ui\Config\Loader\WidgetLoaderInterface;

/**
 * Class WidgetParameterTypeParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetParameterTypeParser implements ParameterTypeTypeParserInterface
{
    /**
     * @var WidgetLoaderInterface
     */
    private $widgetLoader;

    /**
     * @param WidgetLoaderInterface $widgetLoader
     */
    public function __construct(WidgetLoaderInterface $widgetLoader)
    {
        $this->widgetLoader = $widgetLoader;
    }

    /**
     * Determines whether an argument is valid for the parameter
     *
     * @param WidgetParameterType $type
     * @param mixed $value
     * @return bool
     */
    public function argumentIsValid(
        WidgetParameterType $type,
        $value
    ) {
        return is_array($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToParserCallableMap()
    {
        return [
            WidgetParameterType::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToArgumentValidityCallableMap()
    {
        return [
            WidgetParameterType::TYPE => [$this, 'argumentIsValid']
        ];
    }

    /**
     * Fetches the actual argument value for this type from its raw value
     *
     * @param WidgetParameterType $type
     * @param array $rawValue
     * @return WidgetNodeInterface
     */
    public function parseArgument(
        WidgetParameterType $type,
        array $rawValue
    ) {
        return $this->widgetLoader->loadWidget($rawValue, $type->getName());
    }
}
