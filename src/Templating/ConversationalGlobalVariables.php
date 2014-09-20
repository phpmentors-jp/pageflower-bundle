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

namespace PHPMentors\PageflowerBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

class ConversationalGlobalVariables extends GlobalVariables
{
    /**
     * @return PHPMentors\PageflowerBundle\Conversation\ConversationContext
     */
    public function getConversation()
    {
        return $this->container->get('phpmentors_pageflower.conversation_context');
    }
}
