<?php
/*
 * Copyright (c) 2014-2015 KUBO Atsuhiro <kubo@iteman.jp>,
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
use Stagehand\FSM\StateMachine\StateMachineBuilder;

class PageflowBuilder
{
    /**
     * @var string[]
     */
    private $endPages = array();

    /**
     * @var string[]
     */
    private $pages = array();

    /**
     * @var string
     */
    private $startPage;

    /**
     * @var StateMachineBuilder
     */
    private $stateMachineBuilder;

    /**
     * @var array
     */
    private $transitions = array();

    /**
     * @param string $pageflowId
     */
    public function __construct($pageflowId)
    {
        $this->stateMachineBuilder = new StateMachineBuilder(new Pageflow($pageflowId));
    }

    /**
     * @param string $pageId
     */
    public function setStartPage($pageId)
    {
        $this->addPage($pageId);
        $this->startPage = $pageId;
    }

    /**
     * @param string $pageId
     */
    public function addPage($pageId)
    {
        $this->pages[] = $pageId;
    }

    /**
     * @param string $pageId
     */
    public function addEndPage($pageId)
    {
        $this->addPage($pageId);
        $this->endPages[] = $pageId;

        return $this;
    }

    /**
     * @param string $pageId
     */
    public function addTransition($pageId, $nextPageId)
    {
        $this->transitions[] = array($pageId, $nextPageId);
    }

    /**
     * @return Pageflow
     */
    public function build()
    {
        foreach ($this->pages as $pageId) {
            $this->stateMachineBuilder->addState($pageId);
        }

        foreach ($this->transitions as $transition) {
            list($pageId, $nextPageId) = $transition;
            $this->stateMachineBuilder->addTransition($pageId, $nextPageId, $nextPageId);
        }

        $this->stateMachineBuilder->setStartState($this->startPage);

        foreach ($this->endPages as $pageId) {
            $this->stateMachineBuilder->setEndState($pageId, StateInterface::STATE_FINAL);
        }

        return $this->stateMachineBuilder->getStateMachine();
    }
}
