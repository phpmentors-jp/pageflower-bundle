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

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class ConversationalSession extends Session
{
    /**
     * @var string
     */
    private $conversationName;

    /**
     * {@inheritDoc}
     */
    public function __construct(SessionStorageInterface $storage = null, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null, ConversationBag $conversations = null)
    {
        parent::__construct($storage, $attributes, $flashes);

        $this->conversationName = $conversations->getName();
        $this->registerBag($conversations);
    }

    /**
     * @return ConversationBag
     */
    public function getConversationBag()
    {
        return $this->getBag($this->conversationName);
    }
}
