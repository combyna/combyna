<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context;

/**
 * Class RootValidationContext
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RootValidationContext extends AbstractSpecificValidationContext
    implements RootValidationContextInterface
{
    /**
     * @var RootGenericValidationContextInterface
     */
    protected $genericContext;

    /**
     * @param RootGenericValidationContextInterface $rootContext
     */
    public function __construct(RootGenericValidationContextInterface $rootContext)
    {
        parent::__construct($rootContext);
    }

    /**
     * {@inheritdoc}
     */
    public function throwIfViolated()
    {
        $this->genericContext->throwIfViolated();
    }
}
