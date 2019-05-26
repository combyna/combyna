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
 * Class TypeParameterType
 *
 * Defines a parameter that references a Type from the type system
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TypeParameterType implements ParameterTypeInterface
{
    const TYPE = 'type';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @param string $contextDescription
     */
    public function __construct($contextDescription)
    {
        $this->contextDescription = $contextDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return 'a type for ' . $this->contextDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }
}
