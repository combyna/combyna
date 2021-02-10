<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Constraint;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Ui\Behaviour\Query\Specifier\CaptureIsDefinedQuerySpecifier;
use Combyna\Component\Ui\Validation\Query\CaptureIsDefinedQuery;
use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class CaptureIsDefinedConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureIsDefinedConstraint implements ConstraintInterface
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
     * Creates a CaptureIsDefinedQuery for this constraint
     *
     * @return CaptureIsDefinedQuery
     */
    public function createQuery()
    {
        return $this->intention === CaptureIsDefinedQuery::INTENTION_TO_READ ?
            CaptureIsDefinedQuery::createIntendingToRead($this->captureName) :
            CaptureIsDefinedQuery::createIntendingToSet($this->captureName);
    }

    /**
     * Fetches the name of the capture being referenced
     *
     * @return string
     */
    public function getCaptureName()
    {
        return $this->captureName;
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

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $querySpecifier instanceof CaptureIsDefinedQuerySpecifier &&
            $querySpecifier->getCaptureName() === $this->captureName &&
            $querySpecifier->getIntention() === $this->intention;
    }
}
