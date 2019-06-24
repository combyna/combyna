<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Validation\Context\Specifier;

use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;

/**
 * Class ExpressionBagContextSpecifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionBagContextSpecifier implements SubValidationContextSpecifierInterface
{
    /**
     * @var ExpressionNodeInterface[]
     */
    private $expressionNodes;

    /**
     * @param ExpressionNodeInterface[] $expressionNodes
     */
    public function __construct(array $expressionNodes)
    {
        $this->expressionNodes = $expressionNodes;
    }

    /**
     * Fetches the expressions (keyed by name) in the bag
     *
     * @return ExpressionNodeInterface[]
     */
    public function getExpressionNodes()
    {
        return $this->expressionNodes;
    }
}
