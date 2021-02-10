<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Type;

/**
 * Class AbstractTypeDeterminer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractTypeDeterminer implements TypeDeterminerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStructuredChildNodes()
    {
        return []; // Most type determiners won't have any child nodes
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return static::TYPE;
    }
}
