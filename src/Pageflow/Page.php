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

use Stagehand\FSM\State\StateInterface;

class Page
{
    /**
     * @var \Stagehand\FSM\State\StateInterface
     */
    private $state;

    /**
     * @param \Stagehand\FSM\State\StateInterface $state
     */
    public function __construct(StateInterface $state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getPageId()
    {
        return $this->state->getStateId();
    }

    /**
     * @return boolean
     */
    public function isEndPage()
    {
        return $this->state->isEndState();
    }
}
