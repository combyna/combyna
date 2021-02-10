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

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Expression\Config\Act\Assurance\UnknownAssuranceNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;

/**
 * Class DelegatingAssuranceLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingAssuranceLoader implements AssuranceLoaderInterface, DelegatorInterface
{
    /**
     * @var AssuranceTypeLoaderInterface[]
     */
    private $loaders = [];

    /**
     * @param AssuranceTypeLoaderInterface $assuranceTypeLoader
     */
    public function addLoader(AssuranceTypeLoaderInterface $assuranceTypeLoader)
    {
        $this->loaders[$assuranceTypeLoader->getConstraint()] = $assuranceTypeLoader;
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
        if (!array_key_exists($constraintName, $this->loaders)) {
            // No loader is registered for assurances of this type
            return new UnknownAssuranceNode(
                $assuredStaticName,
                $constraintName,
                'constraint is unknown'
            );
        }

        return $this->loaders[$constraintName]->load(
            $assuredStaticName,
            $constraintName,
            $expressionNode,
            $extra
        );
    }
}
