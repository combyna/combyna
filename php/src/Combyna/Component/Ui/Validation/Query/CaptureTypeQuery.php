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
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;

/**
 * Class CaptureTypeQuery
 *
 * Fetches the type a capture is actually defined with
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureTypeQuery implements ResultTypeQueryInterface
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
        return sprintf(
            'The type of the capture "%s"',
            $this->captureName
        );
    }

    /**
     * Fetches the name of the capture to fetch the type of
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
