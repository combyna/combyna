<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Widget;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Environment\Library\LibraryInterface;

/**
 * Class WidgetDefinitionRepository
 *
 * A facade to allow addressing all widget definitions defined by installed libraries or the app itself
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionRepository implements WidgetDefinitionRepositoryInterface
{
    /**
     * @var WidgetDefinitionCollectionInterface
     */
    private $appWidgetDefinitionCollection;

    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @param EnvironmentInterface $environment
     * @param WidgetDefinitionCollectionInterface $appWidgetDefinitionCollection
     */
    public function __construct(
        EnvironmentInterface $environment,
        WidgetDefinitionCollectionInterface $appWidgetDefinitionCollection
    ) {
        $this->appWidgetDefinitionCollection = $appWidgetDefinitionCollection;
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($libraryName, $widgetDefinitionName)
    {
        if ($libraryName === LibraryInterface::APP) {
            return $this->appWidgetDefinitionCollection->getByName($widgetDefinitionName);
        }

        return $this->environment->getWidgetDefinitionByName($libraryName, $widgetDefinitionName);
    }
}
