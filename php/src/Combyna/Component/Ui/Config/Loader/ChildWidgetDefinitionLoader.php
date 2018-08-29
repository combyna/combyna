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

use Combyna\Component\Ui\Config\Act\ChildWidgetDefinitionNode;

/**
 * Class ChildWidgetDefinitionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildWidgetDefinitionLoader implements ChildWidgetDefinitionLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadChildWidgetDefinition($name, array $childConfig)
    {
        return new ChildWidgetDefinitionNode($name);
    }
}
