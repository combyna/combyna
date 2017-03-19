<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader;

use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Ui\ViewInterface;

/**
 * Interface ViewLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewLoaderInterface
{
    /**
     * Creates a view from a config array
     *
     * @param string $name
     * @param array $viewConfig
     * @param EnvironmentNode $environmentNode
     * @return ViewInterface
     */
    public function loadView($name, array $viewConfig, EnvironmentNode $environmentNode);
}
