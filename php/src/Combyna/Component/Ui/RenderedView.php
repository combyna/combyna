<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui;

use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class RenderedView
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RenderedView implements RenderedViewInterface
{
    /**
     * @var StaticBagInterface
     */
    private $attributeStaticBag;

    /**
     * @var RenderedWidgetInterface
     */
    private $renderedRootWidget;

    /**
     * @var ViewInterface
     */
    private $view;

    /**
     * @param ViewInterface $view
     * @param StaticBagInterface $attributeStaticBag
     * @param RenderedWidgetInterface $renderedRootWidget
     */
    public function __construct(
        ViewInterface $view,
        StaticBagInterface $attributeStaticBag,
        RenderedWidgetInterface $renderedRootWidget
    ) {
        $view->assertValidAttributeStaticBag($attributeStaticBag);

        $this->attributeStaticBag = $attributeStaticBag;
        $this->renderedRootWidget = $renderedRootWidget;
        $this->view = $view;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        return $this->attributeStaticBag->getStatic($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getRootWidget()
    {
        return $this->renderedRootWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewName()
    {
        return $this->view->getName();
    }
}
