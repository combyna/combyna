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

/**
 * Class WidgetParameterType
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetParameterType implements ParameterTypeInterface
{
    const TYPE = 'widget';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @var string|int
     */
    private $name;

    /**
     * @param string|int $name
     * @param string $contextDescription
     */
    public function __construct($name, $contextDescription)
    {
        $this->contextDescription = $contextDescription;
        $this->name = $name;
    }

    /**
     * Fetches the name of the widget, if specified
     *
     * @return string|int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return 'a widget ' .
            ($this->name !== null ? '"' . $this->name . '"' : '') .
            ' for ' . $this->contextDescription;
    }
}
