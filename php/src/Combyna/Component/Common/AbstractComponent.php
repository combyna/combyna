<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Common;

use ReflectionClass;

/**
 * Class AbstractComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractComponent implements ComponentInterface
{
    /**
     * {@inheritdoc]
     */
    public function getContainerExtension()
    {
        $extensionClass = $this->getNamespace() . '\\DependencyInjection\\' . $this->getName() . 'Extension';

        if (!class_exists($extensionClass)) {
            return null;
        }

        return new $extensionClass($this);
    }

    /**
     * {@inheritdoc]
     */
    public function getDirectory()
    {
        $reflectionClass = new ReflectionClass(static::class);

        return dirname($reflectionClass->getFileName());
    }

    /**
     * {@inheritdoc]
     */
    public function getName()
    {
        preg_match('@([^\\\\]+)Component$@', static::class, $matches);

        return $matches[1];
    }

    /**
     * {@inheritdoc]
     */
    public function getNamespace()
    {
        return preg_replace('@\\\\[^\\\\]+$@', '', static::class);
    }
}
