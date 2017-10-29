<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class Route
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Route implements RouteInterface
{
    /**
     * @var FixedStaticBagModelInterface
     */
    private $attributeBagModel;

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
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param string $pageViewName
     */
    public function __construct($name, FixedStaticBagModelInterface $attributeBagModel, $pageViewName)
    {
        $this->attributeBagModel = $attributeBagModel;
        $this->name = $name;
        $this->pageViewName = $pageViewName;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidArgumentBag(StaticBagInterface $argumentBag)
    {
        // TODO: Implement assertValidArgumentBag() method.
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
}
