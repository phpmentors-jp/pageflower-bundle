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
use PHPMentors\DomainKata\Repository\RepositoryInterface;

class ConversationRepository implements RepositoryInterface
{
    /**
     * @var ConversationCollectionInterface
     */
    private $conversationCollection;

    /**
     * {@inheritdoc}
     */
    public function add(EntityInterface $entity)
    {
        assert($entity instanceof Conversation);

        $this->conversationCollection[$entity->getConversationId()] = $entity;
    }

    /**
     * @param ConversationCollectionInterface $conversationCollection
     */
    public function setConversationCollection(ConversationCollectionInterface $conversationCollection)
    {
        $this->conversationCollection = $conversationCollection;
    }

    /**
     * @param string $conversationId
     *
     * @return Conversation
     */
    public function findByConversationId($conversationId)
    {
        return $this->conversationCollection[$conversationId];
    }

    /**
     * {@inheritdoc}
     */
    public function remove(EntityInterface $entity)
    {
        assert($entity instanceof Conversation);

        unset($this->conversationCollection[$entity->getConversationId()]);
    }
}
