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

use Combyna\Component\Config\Loader\ConfigParserInterface;

/**
 * Interface WidgetConfigParserInterface
 *
 * Encapsulates parsing data from a config array (eg. from a YAML config file) for a widget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetConfigParserInterface extends ConfigParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parseArguments(array $config, array $parameterList = []);
}
