<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html;

/**
 * Class DocumentFragment
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DocumentFragment implements HtmlNodeInterface
{
    /**
     * @var HtmlNodeInterface[]
     */
    private $childNodes;

    /**
     * @param HtmlNodeInterface[] $childNodes
     */
    public function __construct(array $childNodes = [])
    {
        $this->childNodes = $childNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $childNodeArrays = [];

        foreach ($this->childNodes as $childNode) {
            $childNodeArrays[] = $childNode->toArray();
        }

        return [
            'type' => 'fragment',
            'children' => $childNodeArrays
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toGenericArray()
    {
        return $this->toArray()['children'];
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $childHtml = '';

        foreach ($this->childNodes as $childNode) {
            $childHtml .= $childNode->toHtml();
        }

        return $childHtml;
    }
}
