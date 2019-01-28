<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\ActNodeQueryInterface;

/**
 * Class CaptureDefinitionNodeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureDefinitionNodeQuery implements ActNodeQueryInterface
{
    /**
     * @var string
     */
    private $captureName;

    /**
     * @param string $captureName
     */
    public function __construct($captureName)
    {
        $this->captureName = $captureName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return sprintf('The definition node for the capture "%s"', $this->captureName);
    }

    /**
     * Fetches the name of the capture to fetch the definition node of
     *
     * @return string
     */
    public function getCaptureName()
    {
        return $this->captureName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
