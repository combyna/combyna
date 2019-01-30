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

/**
 * Class OptionalParameter
 *
 * Decorates parameters, making them optional. Parameters are required by default.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class OptionalParameter implements ParameterInterface
{
    const TYPE = 'optional';

    /**
     * @var mixed|null
     */
    private $defaultValue;

    /**
     * @var ParameterInterface
     */
    private $wrappedParameter;

    /**
     * @param ParameterInterface $wrappedParameter
     * @param mixed|null $defaultValue
     */
    public function __construct(ParameterInterface $wrappedParameter, $defaultValue = null)
    {
        $this->defaultValue = $defaultValue;
        $this->wrappedParameter = $wrappedParameter;
    }

    /**
     * Fetches the default argument value for the parameter
     *
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->wrappedParameter->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * Fetches the wrapped parameter
     *
     * @return ParameterInterface
     */
    public function getWrappedParameter()
    {
        return $this->wrappedParameter;
    }
}
