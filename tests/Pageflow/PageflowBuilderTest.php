<?php
/*
 * Copyright (c) 2015 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentorsPageflowerBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace PHPMentors\PageflowerBundle\Conversation;

use PHPMentors\PageflowerBundle\Pageflow\PageflowBuilder;
use Stagehand\FSM\State\StateInterface;

/**
 * @since Class available since Release 1.1.0
 */
class PageflowBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function build()
    {
        $pageflowBuilder = new PageflowBuilder(__CLASS__);
        $pageflowBuilder->setStartPage('input');
        $pageflowBuilder->addPage('confirmation');
        $pageflowBuilder->addEndPage('success');
        $pageflowBuilder->addTransition('input', 'confirmation');
        $pageflowBuilder->addTransition('confirmation', 'success');
        $pageflowBuilder->addTransition('confirmation', 'input');
        $pageflow = $pageflowBuilder->build();

        $this->assertThat($pageflow, $this->isInstanceOf('PHPMentors\PageflowerBundle\Pageflow\Pageflow'));

        $pageflow->start();

        $this->assertThat($pageflow->getCurrentPage()->getPageId(), $this->equalTo('input'));

        $pageflow->triggerEvent('confirmation');

        $this->assertThat($pageflow->getCurrentPage()->getPageId(), $this->equalTo('confirmation'));

        $pageflow->triggerEvent('input');

        $this->assertThat($pageflow->getCurrentPage()->getPageId(), $this->equalTo('input'));

        $pageflow->triggerEvent('confirmation');
        $pageflow->triggerEvent('success');

        $this->assertThat($pageflow->getCurrentPage()->getPageId(), $this->equalTo('success'));
        $this->assertThat($pageflow->getCurrentPage()->isEndPage(), $this->isTrue());

        $pageflow->triggerEvent(StateInterface::STATE_FINAL);

        $this->assertThat($pageflow->getCurrentPage()->getPageId(), $this->equalTo(StateInterface::STATE_FINAL));
    }
}
