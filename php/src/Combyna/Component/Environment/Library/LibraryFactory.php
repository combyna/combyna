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

/**
 * Class LibraryFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LibraryFactory implements LibraryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create($name, array $functions, array $widgetDefinitions)
    {
        return new Library($name, $functions, $widgetDefinitions);
    }
}
