<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Behaviour\Query\Specifier;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Ui\Validation\Query\CaptureIsDefinedQuery;

/**
 * Class CaptureIsDefinedQuerySpecifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureIsDefinedQuerySpecifier implements QuerySpecifierInterface
{
    /**
     * @var string
     */
    private $captureName;

    /**
     * @var string
     */
    private $intention;

    /**
     * @param string $captureName
     * @return self
     */
    public static function createIntendingToRead($captureName)
    {
        return new self($captureName, CaptureIsDefinedQuery::INTENTION_TO_READ);
    }

    /**
     * @param string $captureName
     * @return self
     */
    public static function createIntendingToSet($captureName)
    {
        return new self($captureName, CaptureIsDefinedQuery::INTENTION_TO_SET);
    }

    /**
     * @param string $captureName
     * @param string $intention
     */
    private function __construct($captureName, $intention)
    {
        $this->captureName = $captureName;
        $this->intention = $intention;
    }

    /**
     * Fetches the name of the capture to query the existence of
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
    public function getDescription()
    {
        return sprintf(
            'A query for whether the capture "%s" is defined %s',
            $this->captureName,
            $this->intention
        );
    }

    /**
     * Fetches the intention of making the query (whether to set the capture or read it)
     *
     * @return string
     */
    public function getIntention()
    {
        return $this->intention;
    }
}
