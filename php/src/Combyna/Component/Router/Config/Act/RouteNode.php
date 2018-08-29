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

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Ui\Validation\Constraint\PageViewExistsConstraint;

/**
 * Class RouteNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteNode extends AbstractActNode
{
    const TYPE = 'route';

    /**
     * @var FixedStaticBagModelNode
     */
    private $attributeBagModelNode;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $pageViewName;

    /**
     * @param string $name
     * @param FixedStaticBagModelNode $attributeBagModelNode
     * @param string $pageViewName
     */
    public function __construct($name, FixedStaticBagModelNode $attributeBagModelNode, $pageViewName)
    {
        $this->attributeBagModelNode = $attributeBagModelNode;
        $this->name = $name;
        $this->pageViewName = $pageViewName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->attributeBagModelNode);

        $specBuilder->addConstraint(new PageViewExistsConstraint($this->pageViewName));
    }

    /**
     * Fetches the model for the attribute static bag this route expects to be extracted from its route segments
     *
     * @return FixedStaticBagModelNode
     */
    public function getAttributeBagModel()
    {
        return $this->attributeBagModelNode;
    }

    /**
     * Fetches the unique name of this route
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fetches the name of the page view that should be rendered for this route
     *
     * @return string
     */
    public function getPageViewName()
    {
        return $this->pageViewName;
    }
}
