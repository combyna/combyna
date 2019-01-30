<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader\Assurance;

use Combyna\Component\Expression\Assurance\NonZeroNumberAssurance;
use Combyna\Component\Expression\Config\Act\Assurance\NonZeroNumberAssuranceNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;

/**
 * Class NonZeroNumberAssuranceLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NonZeroNumberAssuranceLoader implements AssuranceTypeLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraint()
    {
        return NonZeroNumberAssurance::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function load(
        $assuredStaticName,
        $constraintName,
        ExpressionNodeInterface $expressionNode,
        array $extra
    ) {
        // NB: Extra args are not used by this assurance type

        return new NonZeroNumberAssuranceNode($expressionNode, $assuredStaticName);
    }
}
