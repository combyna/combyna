<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Act;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Router\Validation\Constraint\ValidParameterBagForUrlPatternConstraint;
use Combyna\Component\Ui\Validation\Constraint\PageViewExistsConstraint;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class RouteNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteNode extends AbstractActNode implements RouteNodeInterface
{
    const TYPE = 'route';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $pageViewName;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $parameterBagModelNode;

    /**
     * @var string
     */
    private $urlPattern;

    /**
     * @param string $name
     * @param string $urlPattern
     * @param FixedStaticBagModelNodeInterface $parameterBagModelNode
     * @param string $pageViewName
     */
    public function __construct($name, $urlPattern, FixedStaticBagModelNodeInterface $parameterBagModelNode, $pageViewName)
    {
        $this->name = $name;
        $this->pageViewName = $pageViewName;
        $this->parameterBagModelNode = $parameterBagModelNode;
        $this->urlPattern = $urlPattern;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->parameterBagModelNode);

        $specBuilder->addConstraint(
            // Make sure that all braced parameter placeholders in the URL pattern
            // are defined as parameters with types, and that all parameter definitions
            // have a matching placeholder in the URL pattern
            new ValidParameterBagForUrlPatternConstraint(
                $this->urlPattern,
                $this->parameterBagModelNode
            )
        );
        $specBuilder->addConstraint(new PageViewExistsConstraint($this->pageViewName));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageViewName()
    {
        return $this->pageViewName;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterBagModel(QueryRequirementInterface $queryRequirement)
    {
        return $this->parameterBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }
}
