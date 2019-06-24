<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Exotic;

use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Interface ExoticTypeDeterminerFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExoticTypeDeterminerFactoryInterface
{
    /**
     * Creates an exotic type determiner for the given config and source validation context
     *
     * @param string $determinerName
     * @param array $config
     * @param ValidationContextInterface $sourceValidationContext
     * @return ExoticTypeDeterminerInterface
     */
    public function createDeterminer(
        $determinerName,
        array $config,
        ValidationContextInterface $sourceValidationContext
    );
}
