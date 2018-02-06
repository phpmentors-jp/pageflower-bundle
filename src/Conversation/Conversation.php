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
use PHPMentors\PageflowerBundle\Pageflow\Page;
use PHPMentors\PageflowerBundle\Pageflow\Pageflow;
use Stagehand\FSM\State\StateInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class Conversation implements EntityInterface
{
    /**
     * @var ParameterBag
     */
    public $attributes;

    /**
     * @var string
     */
    private $conversationId;

    /**
     * @var Pageflow
     */
    private $pageflow;

    /**
     * @var int
     */
    private $stepCount = 0;

    /**
     * @var int
     *
     * @since Property available since Release 1.1.0
     */
    private $stepCountOnEndPage;

    /**
     * @param string   $conversationId
     * @param Pageflow $pageflow
     */
    public function __construct($conversationId, Pageflow $pageflow)
    {
        $this->attributes = new ParameterBag();
        $this->conversationId = $conversationId;
        $this->pageflow = $pageflow;
    }

    /**
     * @return string
     */
    public function getConversationId()
    {
        return $this->conversationId;
    }

    /**
     * @return Pageflow
     */
    public function getPageflow()
    {
        return $this->pageflow;
    }

    /**
     * @return Page
     */
    public function getCurrentPage()
    {
        return $this->pageflow->getCurrentPage();
    }

    /**
     * @return Page
     */
    public function getPreviousPage()
    {
        return $this->pageflow->getPreviousPage();
    }

    public function start()
    {
        $this->pageflow->start();
    }

    public function end()
    {
        $this->pageflow->triggerEvent(StateInterface::STATE_FINAL);
    }

    /**
     * @param string $pageId
     */
    public function transition($pageId)
    {
        $this->pageflow->triggerEvent($pageId);
    }

    /**
     * @since Method available since Release 1.1.0
     */
    public function increaseStepCount()
    {
        ++$this->stepCount;
    }

    /**
     * @param int $conversationCount
     *
     * @deprecated Deprecated since version 1.1.0, to be removed in 2.0.0.
     */
    public function increaseConversationCount()
    {
        $this->increaseStepCount();
    }

    /**
     * @return bool
     *
     * @since Method available since Release 1.1.0
     */
    public function onFirstStep()
    {
        return $this->stepCount <= 1;
    }

    /**
     * @return bool
     *
     * @deprecated Deprecated since version 1.1.0, to be removed in 2.0.0.
     */
    public function isFirstTime()
    {
        return $this->onFirstStep();
    }

    /**
     * @since Method available since Release 1.1.0
     */
    public function logStepCountOnEndPage()
    {
        $this->stepCountOnEndPage = $this->stepCount;
    }

    /**
     * @return int
     *
     * @since Method available since Release 1.1.0
     */
    public function getStepCount()
    {
        return $this->stepCount;
    }

    /**
     * @return int|null
     *
     * @since Method available since Release 1.1.0
     */
    public function getStepCountOnEndPage()
    {
        return $this->stepCountOnEndPage;
    }
}
