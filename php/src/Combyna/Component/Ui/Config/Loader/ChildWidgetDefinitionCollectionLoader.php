<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader;

/**
 * Class ChildWidgetDefinitionCollectionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildWidgetDefinitionCollectionLoader implements ChildWidgetDefinitionCollectionLoaderInterface
{
    /**
     * @var ChildWidgetDefinitionLoaderInterface
     */
    private $childDefinitionLoader;

    /**
     * @param ChildWidgetDefinitionLoaderInterface $childDefinitionLoader
     */
    public function __construct(ChildWidgetDefinitionLoaderInterface $childDefinitionLoader)
    {
        $this->childDefinitionLoader = $childDefinitionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadCollection(array $childrenConfig)
    {
        $childDefinitions = [];

        foreach ($childrenConfig as $childName => $childConfig) {
            $childDefinitions[$childName] = $this->childDefinitionLoader->loadChildWidgetDefinition(
                $childName,
                // TODO: Fail if non-array, eg. an int?
                is_array($childConfig) ? $childConfig : []
            );
        }

        return $childDefinitions;
    }
}
