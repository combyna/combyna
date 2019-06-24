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
use LogicException;

/**
 * Class PositionalParameter
 *
 * Defines a parameter at a specific index
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PositionalParameter implements ParameterInterface
{
    const TYPE = 'positional';

    /**
     * @var string
     */
    private $name;

    /**
     * @var int|null
     */
    private $position = null;

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
     * Fetches the position of this parameter, if set
     *
     * @return int
     * @throws LogicException
     */
    public function getPosition()
    {
        if ($this->position === null) {
            throw new LogicException('Positional parameter position has not been set');
        }

        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * Sets the position of this parameter
     *
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}
