<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment;

/**
 * Class EnvironmentFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentFactory implements EnvironmentFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $libraries = [])
    {
        return new Environment($libraries);
    }
}
