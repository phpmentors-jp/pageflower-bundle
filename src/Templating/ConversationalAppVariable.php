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

namespace PHPMentors\PageflowerBundle\Templating;

use PHPMentors\PageflowerBundle\Conversation\ConversationContext;
use Symfony\Bridge\Twig\AppVariable;

class ConversationalAppVariable extends AppVariable
{
    /**
     * @var ConversationContext
     *
     * @since Property available since Release 1.4.0
     */
    private $conversationContext;

    /**
     * @param ConversationContext $conversationContext
     *
     * @since Method available since Release 1.4.0
     */
    public function __construct(ConversationContext $conversationContext)
    {
        $this->conversationContext = $conversationContext;
    }

    /**
     * @return ConversationContext
     */
    public function getConversation()
    {
        return $this->conversationContext;
    }
}
