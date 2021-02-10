<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Type;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class UnresolvedTypeDeterminer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnresolvedTypeDeterminer extends AbstractTypeDeterminer
{
    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @param string $contextDescription
     */
    public function __construct($contextDescription)
    {
        $this->contextDescription = $contextDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        return new UnresolvedType($this->contextDescription, $validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
