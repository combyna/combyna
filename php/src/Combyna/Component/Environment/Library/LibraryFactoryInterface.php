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
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;

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
     * @param FunctionCollectionInterface $functionCollection
     * @param EventDefinitionCollectionInterface $eventDefinitionCollection
     * @param SignalDefinitionCollectionInterface $signalDefinitionCollection
     * @param WidgetDefinitionInterface[] $widgetDefinitions
     * @return LibraryInterface
     */
    public function create(
        $name,
        FunctionCollectionInterface $functionCollection,
        EventDefinitionCollectionInterface $eventDefinitionCollection,
        SignalDefinitionCollectionInterface $signalDefinitionCollection,
        array $widgetDefinitions
    );
}
