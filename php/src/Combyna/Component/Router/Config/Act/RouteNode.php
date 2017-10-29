<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Act;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

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

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->attributeBagModelNode->validate($subValidationContext);
    }
}
