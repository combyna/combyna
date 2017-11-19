<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context;

/**
 * Class AssuredValidationContext
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredValidationContext extends AbstractSpecificValidationContext
    implements AssuredValidationContextInterface
{
    /**
     * @param GenericValidationContextInterface $genericContext
     */
    public function __construct(GenericValidationContextInterface $genericContext)
    {
        parent::__construct($genericContext);
    }
}
