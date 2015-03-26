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

namespace PHPMentors\PageflowerBundle\Conversation;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

use PHPMentors\PageflowerBundle\Controller\ConversationalControllerInterface;
use PHPMentors\PageflowerBundle\Controller\ReflectionConversationalController;
use PHPMentors\PageflowerBundle\Pageflow\Page;

class ConversationContext
{
    /**
     * @var Conversation
     */
    private $conversation;

    /**
     * @var string
     */
    private $conversationParameterName;

    /**
     * @var ConversationalControllerInterface
     */
    private $conversationalController;

    /**
     * @var ReflectionConversationalController
     */
    private $reflectionConversationalController;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param string          $conversationParameterName
     * @param RouterInterface $router
     */
    public function __construct($conversationParameterName, RouterInterface $router)
    {
        $this->conversationParameterName = $conversationParameterName;
        $this->router = $router;
    }

    /**
     * @param Conversation $conversation
     */
    public function setConversation($conversation)
    {
        $this->conversation = $conversation;
    }

    /**
     * @return Conversation
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * @return string
     */
    public function getConversationParameterName()
    {
        return $this->conversationParameterName;
    }

    /**
     * @param ConversationalControllerInterface $conversationalController
     */
    public function setConversationalController(ConversationalControllerInterface $conversationalController)
    {
        $this->conversationalController = $conversationalController;
    }

    /**
     * @return ConversationalControllerInterface
     */
    public function getConversationalController()
    {
        return $this->conversationalController;
    }

    /**
     * @param ReflectionConversationalController $reflectionConversationalController
     */
    public function setReflectionConversationalController(ReflectionConversationalController $reflectionConversationalController)
    {
        $this->reflectionConversationalController = $reflectionConversationalController;
    }

    /**
     * @return ReflectionConversationalController
     */
    public function getReflectionConversationalController()
    {
        return $this->reflectionConversationalController;
    }

    /**
     * {@inheritDoc}
     */
    public function generateUrl($routeName, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if ($this->conversation !== null) {
            $route = $this->router->getRouteCollection()->get($routeName);
            if ($route !== null) {
                $controllerName = $route->getDefault('_controller');
                if (strpos($controllerName, '::') === false && substr_count($controllerName, ':') == 1) {
                    $controllerServiceId = substr($controllerName, 0, strpos($controllerName, ':'));
                    if ($controllerServiceId == $this->conversation->getPageflow()->getPageflowId()) {
                        $parameters[$this->conversationParameterName] = $this->conversation->getConversationId();
                    }
                }
            }
        }

        return $this->router->generate($routeName, $parameters, $referenceType);
    }

    /**
     * @return Page
     */
    public function getCurrentPage()
    {
        if ($this->conversation === null) {
            return null;
        } else {
            return $this->conversation->getCurrentPage();
        }
    }

    /**
     * @return Page
     */
    public function getPreviousPage()
    {
        if ($this->conversation === null) {
            return null;
        } else {
            return $this->conversation->getPreviousPage();
        }
    }

    /**
     * @return bool
     * @since Method available since Release 1.1.0
     */
    public function onFirstStep()
    {
        if ($this->conversation === null) {
            return null;
        } else {
            return $this->conversation->onFirstStep();
        }
    }

    /**
     * @return bool
     * @deprecated Deprecated since version 1.1.0, to be removed in 2.0.0.
     */
    public function isFirstTime()
    {
        return $this->onFirstStep();
    }
}
