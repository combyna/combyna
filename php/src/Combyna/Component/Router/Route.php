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
    private $libraryName;

    /**
     * @var string
     */
    private $routeName;

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
     * @param string $libraryName
     * @param string $routeName
     * @param string $urlPattern
     * @param FixedStaticBagModelInterface $parameterBagModel
     * @param string $pageViewName
     */
    public function __construct(
        $libraryName,
        $routeName,
        $urlPattern,
        FixedStaticBagModelInterface $parameterBagModel,
        $pageViewName
    ) {
        $this->libraryName = $libraryName;
        $this->pageViewName = $pageViewName;
        $this->parameterBagModel = $parameterBagModel;
        $this->routeName = $routeName;
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
    public function getLibraryName()
    {
        return $this->libraryName;
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
    public function getParameterBagModel()
    {
        return $this->parameterBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }
}
