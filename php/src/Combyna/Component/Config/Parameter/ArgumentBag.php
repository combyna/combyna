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
 * Class ArgumentBag
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ArgumentBag implements ArgumentBagInterface
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * @var array
     */
    private $extraArguments;

    /**
     * @param array $arguments
     * @param array $extraArguments
     */
    public function __construct(array $arguments, array $extraArguments)
    {
        $this->arguments = $arguments;
        $this->extraArguments = $extraArguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraArguments()
    {
        return $this->extraArguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedArrayArgument($name)
    {
        return $this->arguments[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedExpressionArgument($name)
    {
        return $this->arguments[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedExpressionBagArgument($name)
    {
        return $this->arguments[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedFixedStaticBagModelArgument($name)
    {
        return $this->arguments[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedStringArgument($name)
    {
        return $this->arguments[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedWidgetArgument($name)
    {
        return $this->arguments[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getNamedTypeArgument($name)
    {
        return $this->arguments[$name];
    }
}
