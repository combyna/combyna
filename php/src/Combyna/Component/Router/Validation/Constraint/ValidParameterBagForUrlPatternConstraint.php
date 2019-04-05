<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Validation\Constraint;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class ValidParameterBagForUrlPatternConstraint
 *
 * Ensures that all parameters in the bag are used at least one in the URL pattern,
 * and that all parameter placeholders in the URL pattern are defined by the bag
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidParameterBagForUrlPatternConstraint implements ConstraintInterface
{
    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $parameterBagModelNode;

    /**
     * @var string
     */
    private $urlPattern;

    /**
     * @param string $urlPattern
     * @param FixedStaticBagModelNodeInterface $parameterBagModelNode
     */
    public function __construct(
        $urlPattern,
        FixedStaticBagModelNodeInterface $parameterBagModelNode
    ) {
        $this->parameterBagModelNode = $parameterBagModelNode;
        $this->urlPattern = $urlPattern;
    }

    /**
     * Fetches the parameter bag model
     *
     * @return FixedStaticBagModelNodeInterface
     */
    public function getParameterBagModel()
    {
        return $this->parameterBagModelNode;
    }

    /**
     * Fetches the URL pattern
     *
     * @return string
     */
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
