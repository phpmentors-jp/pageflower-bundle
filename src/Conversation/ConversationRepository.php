<?php
/*
 * Copyright (c) 2014 KUBO Atsuhiro <kubo@iteman.jp>,
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
use PHPMentors\DomainKata\Repository\RepositoryInterface;

class ConversationRepository implements RepositoryInterface
{
    /**
     * @var \PHPMentors\PageflowerBundle\Conversation\ConversationCollectionInterface
     */
    private $conversationCollection;

    /**
     * {@inheritDoc}
     */
    public function add(EntityInterface $entity)
    {
        /* @var $entity \PHPMentors\PageflowerBundle\Conversation\Conversation */
        $this->conversationCollection[$entity->getConversationId()] = $entity;
    }

    /**
     * @param \PHPMentors\PageflowerBundle\Conversation\ConversationCollectionInterface $conversationCollection
     */
    public function setConversationCollection(ConversationCollectionInterface $conversationCollection)
    {
        $this->conversationCollection = $conversationCollection;
    }

    /**
     * @param  string                                                 $conversationId
     * @return \PHPMentors\PageflowerBundle\Conversation\Conversation
     */
    public function findByConversationId($conversationId)
    {
        return $this->conversationCollection[$conversationId];
    }

    /**
     * {@inheritDoc}
     */
    public function remove(EntityInterface $entity)
    {
        /* @var $entity \PHPMentors\PageflowerBundle\Conversation\Conversation */
        unset($this->conversationCollection[$entity->getConversationId()]);
    }
}
