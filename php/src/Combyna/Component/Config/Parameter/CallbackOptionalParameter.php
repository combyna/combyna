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
 * Class CallbackOptionalParameter
 *
 * Decorates parameters, making them optional. Parameters are required by default.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CallbackOptionalParameter implements ParameterInterface
{
    const TYPE = 'callback-optional';

    /**
     * @var callable
     */
    private $defaultValueCallback;

    /**
     * @var ParameterInterface
     */
    private $wrappedParameter;

    /**
     * @param ParameterInterface $wrappedParameter
     * @param callable $defaultValueCallback
     */
    public function __construct(ParameterInterface $wrappedParameter, callable $defaultValueCallback)
    {
        $this->defaultValueCallback = $defaultValueCallback;
        $this->wrappedParameter = $wrappedParameter;
    }

    /**
     * Fetches the default argument value for the parameter
     *
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        $callback = $this->defaultValueCallback;

        return $callback($this);
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
