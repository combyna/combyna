<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class UnknownExpressionNode
 *
 * Represents a node in the ACT with an unknown type, making it invalid
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownExpressionNode extends AbstractExpressionNode
{
    const TYPE = 'unknown';

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new UnknownType();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $validationContext->addGenericViolation('Node is of unknown type "' . $this->type . '"');
    }
}
