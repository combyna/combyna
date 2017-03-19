<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

use Combyna\Component\Ui\WidgetDefinitionInterface;

/**
 * Interface LibraryFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface LibraryFactoryInterface
{
    /**
     * Creates a new library
     *
     * @param string $name
     * @param FunctionInterface[] $functions
     * @param WidgetDefinitionInterface[] $widgetDefinitions
     * @return Library
     */
    public function create($name, array $functions, array $widgetDefinitions);
}
