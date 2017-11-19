<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Expression\Config\Act\AbstractExpressionNode;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Ui\Store\Expression\SlotExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ViewStoreSlotExpressionNode
 *
 * Fetches the value of a store slot (only accessible from its queries and command/signal handler instructions)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreSlotExpressionNode extends AbstractExpressionNode
{
    const TYPE = SlotExpression::TYPE;

    /**
     * @var string
     */
    private $slotName;

    /**
     * @param string $slotName
     */
    public function __construct($slotName)
    {
        $this->slotName = $slotName;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
//        return $validationContext->getViewStoreQueryType($this->queryName);

        // FIXME!
        return new StaticType(TextExpression::class);
    }

    /**
     * Fetches the name of the slot to be fetched
     *
     * @return string
     */
    public function getSlotName()
    {
        return $this->slotName;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        // ...
    }
}
