<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Violation;

use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\ViolationInterface;

/**
 * Class GenericViolation
 *
 * Represents a validation failure
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GenericViolation implements ViolationInterface
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var SubValidationContextInterface
     */
    private $subValidationContext;

    /**
     * @param SubValidationContextInterface $subValidationContext
     * @param string $description
     */
    public function __construct(
        $description,
        SubValidationContextInterface $subValidationContext
    ) {
        $this->description = $description;
        $this->subValidationContext = $subValidationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->subValidationContext->getPath();
    }
}
