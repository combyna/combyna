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

use Combyna\Component\Event\EventDefinitionCollectionInterface;
use Combyna\Component\Signal\SignalDefinitionCollectionInterface;

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
    public function create(
        $name,
        FunctionCollectionInterface $functionCollection,
        EventDefinitionCollectionInterface $eventDefinitionCollection,
        SignalDefinitionCollectionInterface $signalDefinitionCollection,
        array $widgetDefinitions
    ) {
        return new Library(
            $name,
            $functionCollection,
            $eventDefinitionCollection,
            $signalDefinitionCollection,
            $widgetDefinitions
        );
    }
}
