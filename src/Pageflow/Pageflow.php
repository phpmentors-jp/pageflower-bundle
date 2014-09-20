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

use PHPMentors\DomainKata\Entity\EntityInterface;
use Stagehand\FSM\StateMachine\StateMachine;

class Pageflow extends StateMachine implements EntityInterface
{
    /**
     * @return string
     */
    public function getPageflowId()
    {
        return $this->getStateMachineId();
    }
}
