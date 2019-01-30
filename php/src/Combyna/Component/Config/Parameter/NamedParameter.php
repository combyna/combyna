<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Parameter;

use Combyna\Component\Config\Parameter\Type\ParameterTypeInterface;

/**
 * Class NamedParameter
 *
 * Defines a parameter with a string name
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NamedParameter implements ParameterInterface
{
    const TYPE = 'named';

    /**
     * @var string
     */
    private $name;

    /**
     * @var ParameterTypeInterface
     */
    private $type;

    /**
     * @param string $name
     * @param ParameterTypeInterface $type
     */
    public function __construct($name, ParameterTypeInterface $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fetches the type of this parameter
     *
     * @return ParameterTypeInterface
     */
    public function getParameterType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }
}
