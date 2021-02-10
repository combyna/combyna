<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;

/**
 * Class HomeNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HomeNode extends AbstractActNode
{
    /**
     * @var ExpressionBagNode
     */
    private $attributeExpressionBagNode;

    /**
     * @var string
     */
    private $routeLibraryName;

    /**
     * @var string
     */
    private $routeName;

    /**
     * @param string $routeLibraryName
     * @param string $routeName
     * @param ExpressionBagNode $attributeExpressionBagNode
     */
    public function __construct($routeLibraryName, $routeName, ExpressionBagNode $attributeExpressionBagNode)
    {
        $this->attributeExpressionBagNode = $attributeExpressionBagNode;
        $this->routeLibraryName = $routeLibraryName;
        $this->routeName = $routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->attributeExpressionBagNode);

        $specBuilder->addConstraint(new LibraryExistsConstraint($this->routeLibraryName));
        $specBuilder->addConstraint(new LibraryDefinesRouteConstraint($this->routeLibraryName, $this->routeName));
    }

    /**
     * Fetches the bag of expressions to evaluate for the route's attributes
     *
     * @return ExpressionBagNode
     */
    public function getAttributeExpressionBag()
    {
        return $this->attributeExpressionBagNode;
    }

    /**
     * Fetches the name of the library the home route is defined by
     *
     * @return string
     */
    public function getRouteLibraryName()
    {
        return $this->routeLibraryName;
    }

    /**
     * Fetches the unique name of this route within its library
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }
}
