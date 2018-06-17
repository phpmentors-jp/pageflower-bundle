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

namespace PHPMentors\PageflowerBundle\Session;

use PHPMentors\PageflowerBundle\Conversation\Conversation;
use PHPMentors\PageflowerBundle\Conversation\ConversationCollectionInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

class ConversationBag implements ConversationCollectionInterface, SessionBagInterface
{
    /**
     * @var array
     */
    private $conversations = array();

    /**
     * @var string
     */
    private $name = 'conversations';

    /**
     * @var string
     */
    private $storageKey;

    /**
     * @param string $storageKey
     */
    public function __construct($storageKey)
    {
        $this->storageKey = $storageKey;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $conversations = $this->conversations;
        $this->conversations = array();

        return $conversations;
    }

    /**
     * @return array
     */
    public function getConversations()
    {
        return $this->conversations;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageKey()
    {
        return $this->storageKey;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array &$conversations)
    {
        $this->conversations = &$conversations;
    }

    /**
     * @param string $conversationId
     *
     * @return bool
     */
    public function offsetExists($conversationId)
    {
        return array_key_exists($conversationId, $this->conversations);
    }

    /**
     * @param string $conversationId
     *
     * @return Conversation
     */
    public function offsetGet($conversationId)
    {
        return @$this->conversations[$conversationId];
    }

    /**
     * @param string       $conversationId
     * @param Conversation $conversation
     */
    public function offsetSet($conversationId, $conversation)
    {
        $this->conversations[$conversationId] = $conversation;
    }

    /**
     * @param string $conversationId
     */
    public function offsetUnset($conversationId)
    {
        unset($this->conversations[$conversationId]);
    }
}
