<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Parameter;

/**
 * Class ExtraParameter
 *
 * Allows arbitrary additional parameters to be passed
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExtraParameter implements ParameterInterface
{
    const NAME = '[extra]';
    const TYPE = 'extra';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }
}
