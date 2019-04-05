<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
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
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $pageViewName;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $parameterBagModel;

    /**
     * @var string
     */
    private $urlPattern;

    /**
     * @param string $name
     * @param string $urlPattern
     * @param FixedStaticBagModelInterface $parameterBagModel
     * @param string $pageViewName
     */
    public function __construct($name, $urlPattern, FixedStaticBagModelInterface $parameterBagModel, $pageViewName)
    {
        $this->name = $name;
        $this->pageViewName = $pageViewName;
        $this->parameterBagModel = $parameterBagModel;
        $this->urlPattern = $urlPattern;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidArgumentBag(StaticBagInterface $argumentBag)
    {
        $this->parameterBagModel->assertValidStaticBag($argumentBag);
    }

    /**
     * {@inheritdoc}
     */
    public function generateUrl(StaticBagInterface $argumentBag)
    {
        $this->assertValidArgumentBag($argumentBag);

        // Replace each instance of the parameter placeholder in the URL pattern with its value,
        // eg. `/my/{parameter-placeholder}/path` -> `/my/replaced/path`
        $parameterArgumentPairs = [];

        foreach ($argumentBag->toNativeArray() as $parameterName => $argumentNative) {
            $parameterArgumentPairs['{' . $parameterName . '}'] = $argumentNative;
        }

        return strtr($this->urlPattern, $parameterArgumentPairs);
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
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }
}
