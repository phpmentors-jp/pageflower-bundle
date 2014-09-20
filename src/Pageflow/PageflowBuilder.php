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

namespace PHPMentors\PageflowerBundle\Pageflow;

use Stagehand\FSM\StateMachine\StateMachine;
use Stagehand\FSM\StateMachine\StateMachineBuilder;

class PageflowBuilder extends StateMachineBuilder
{
    /**
     * {@inheritDoc}
     */
    public function __construct($pageflowId)
    {
        parent::__construct(new Pageflow($pageflowId));
    }
}
