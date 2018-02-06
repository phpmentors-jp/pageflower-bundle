<?php
/*
 * Copyright (c) Atsuhiro Kubo <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentorsPageflowerBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace PHPMentors\PageflowerBundle\Conversation;

use PHPMentors\DomainKata\Entity\EntityInterface;
use PHPMentors\DomainKata\Specification\SpecificationInterface;

/**
 * @since Class available since Release 1.1.0
 */
class EndableConversationSpecification implements SpecificationInterface
{
    /**
     * @var bool
     */
    private $endOnNextStepOfEndPage;

    /**
     * @param bool $endOnNextStepOfEndPage
     */
    public function __construct($endOnNextStepOfEndPage)
    {
        $this->endOnNextStepOfEndPage = $endOnNextStepOfEndPage;
    }

    /**
     * {@inheritDoc}
     */
    public function isSatisfiedBy(EntityInterface $entity)
    {
        assert($entity instanceof Conversation);

        if ($entity->getCurrentPage()->isEndPage()) {
            if ($this->endOnNextStepOfEndPage) {
                if ($entity->getStepCountOnEndPage() === null) {
                    return false;
                } else {
                    return $entity->getStepCount() > $entity->getStepCountOnEndPage();
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
