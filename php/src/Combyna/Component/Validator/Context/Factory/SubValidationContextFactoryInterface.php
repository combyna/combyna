<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context\Factory;

/**
 * Interface SubValidationContextFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SubValidationContextFactoryInterface
{
    /**
     * Fetches a map from the FQCN of the classes of specifier that this factory is for
     * to the public method that may be called to create their corresponding context
     *
     * @return callable[]
     */
    public function getSpecifierClassToContextFactoryCallableMap();
}
