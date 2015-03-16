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

namespace PHPMentors\PageflowerBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;

use PHPMentors\PageflowerBundle\Controller\ReflectionConversationalControllerRepository;
use PHPMentors\PageflowerBundle\Conversation\Conversation;
use PHPMentors\PageflowerBundle\Conversation\ConversationContext;
use PHPMentors\PageflowerBundle\Conversation\ConversationContextAwareInterface;
use PHPMentors\PageflowerBundle\Conversation\ConversationRepository;
use PHPMentors\PageflowerBundle\Conversation\EndableConversationSpecification;
use PHPMentors\PageflowerBundle\Pageflow\Pageflow;
use PHPMentors\PageflowerBundle\Pageflow\PageflowRepository;

class ConversationListener implements ConversationContextAwareInterface
{
    /**
     * @var \PHPMentors\PageflowerBundle\Conversation\ConversationContext
     */
    private $conversationContext;

    /**
     * @var \PHPMentors\PageflowerBundle\Conversation\ConversationRepository
     */
    private $conversationRepository;

    /**
     * @var \PHPMentors\PageflowerBundle\Pageflow\PageflowRepository
     */
    private $pageflowRepository;

    /**
     * @var \PHPMentors\PageflowerBundle\Controller\ReflectionConversationalControllerRepository
     */
    private $reflectionConversationalControllerRepository;

    /**
     * @var \Symfony\Component\Security\Core\Util\SecureRandomInterface
     */
    private $secureRandom;

    /**
     * @var EndableConversationSpecification
     * @since Property available since Release 1.1.0
     */
    private $endableConversationSpecification;

    /**
     * @param \PHPMentors\PageflowerBundle\Conversation\ConversationRepository                     $conversationRepository
     * @param \PHPMentors\PageflowerBundle\Pageflow\PageflowRepository                             $pageflowRepository
     * @param \PHPMentors\PageflowerBundle\Controller\ReflectionConversationalControllerRepository $reflectionConversationalControllerRepository
     * @param \Symfony\Component\Security\Core\Util\SecureRandomInterface                          $secureRandom
     * @param EndableConversationSpecification                                                     $endableConversationSpecification
     */
    public function __construct(ConversationRepository $conversationRepository, PageflowRepository $pageflowRepository, ReflectionConversationalControllerRepository $reflectionConversationalControllerRepository, SecureRandomInterface $secureRandom, EndableConversationSpecification $endableConversationSpecification)
    {
        $this->conversationRepository = $conversationRepository;
        $this->pageflowRepository = $pageflowRepository;
        $this->reflectionConversationalControllerRepository = $reflectionConversationalControllerRepository;
        $this->secureRandom = $secureRandom;
        $this->endableConversationSpecification = $endableConversationSpecification;
    }

    /**
     * {@inheritDoc}
     */
    public function setConversationContext(ConversationContext $conversationContext)
    {
        $this->conversationContext = $conversationContext;
    }

    /**
     * @param  \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     * @throws \UnexpectedValueException
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            $controllerName = $event->getRequest()->attributes->get('_controller');
            if (strpos($controllerName, '::') === false && substr_count($controllerName, ':') == 1) {
                $pageflowId = substr($controllerName, 0, strpos($controllerName, ':'));
                $pageflow = $this->pageflowRepository->findByPageflowId($pageflowId);
                if ($pageflow !== null) {
                    list($conversationalController, $action) = $event->getController();

                    $this->conversationRepository->setConversationCollection($event->getRequest()->getSession()->getConversationBag());

                    if ($event->getRequest()->request->has($this->conversationContext->getConversationParameterName())) {
                        $conversationId = $event->getRequest()->request->get($this->conversationContext->getConversationParameterName());
                    } elseif ($event->getRequest()->query->has($this->conversationContext->getConversationParameterName())) {
                        $conversationId = $event->getRequest()->query->get($this->conversationContext->getConversationParameterName());
                    }

                    if (isset($conversationId)) {
                        $conversation = $this->conversationRepository->findByConversationId($conversationId);
                    }

                    if (!isset($conversation)) {
                        $conversation = $this->createConversation($pageflow);
                        $conversation->start();

                        $reflectionConversationalController = $this->reflectionConversationalControllerRepository->findByClass(get_class($conversationalController));
                        if ($reflectionConversationalController === null) {
                            throw new \UnexpectedValueException(sprintf(
                                'ReflectionConversationalController object for controller "%s" is not found.',
                                get_class($conversationalController)
                            ));
                        }

                        foreach ($reflectionConversationalController->getInitMethods() as $initMethod) { /* @var $initMethod \ReflectionMethod */
                            if (is_callable(array($conversationalController, $initMethod->getName()))) {
                                $initMethod->invoke($conversationalController);
                            } else {
                                throw new \UnexpectedValueException(sprintf(
                                    'Init method "%s" for pageflow "%s" is not callable.',
                                    get_class($conversationalController).'::'.$initMethod->getName(),
                                    $pageflow->getPageflowId()
                                ));
                            }
                        }

                        $this->conversationRepository->add($conversation);
                    }

                    $conversation->increaseStepCount();

                    if (!isset($reflectionConversationalController)) {
                        $reflectionConversationalController = $this->reflectionConversationalControllerRepository->findByClass(get_class($conversationalController));
                        if ($reflectionConversationalController === null) {
                            throw new \UnexpectedValueException(sprintf(
                                'ReflectionConversationalController object for controller "%s" is not found.',
                                get_class($conversationalController)
                            ));
                        }
                    }

                    if (!in_array($conversation->getCurrentPage()->getPageId(), $reflectionConversationalController->getAcceptablePages($action))) {
                        throw new AccessDeniedHttpException(sprintf(
                            'Controller "%s" should be accessed when the current page is one of [ %s ], the actual page is "%s".',
                            get_class($conversationalController).'::'.$action,
                            implode(', ', $reflectionConversationalController->getAcceptablePages($action)),
                            $conversation->getCurrentPage()->getPageId()
                        ));
                    }

                    foreach ($reflectionConversationalController->getStatefulProperties() as $statefulProperty) { /* @var $statefulProperty \ReflectionProperty */
                        if ($conversation->attributes->has($statefulProperty->getName())) {
                            if (!$statefulProperty->isPublic()) {
                                $statefulProperty->setAccessible(true);
                            }

                            $statefulProperty->setValue($conversationalController, $conversation->attributes->get($statefulProperty->getName()));

                            if (!$statefulProperty->isPublic()) {
                                $statefulProperty->setAccessible(false);
                            }
                        }
                    }

                    $this->conversationContext->setConversation($conversation);
                    $this->conversationContext->setConversationalController($conversationalController);
                    $this->conversationContext->setReflectionConversationalController($reflectionConversationalController);
                }
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            $conversation = $this->conversationContext->getConversation();
            if ($conversation !== null) {
                if ($this->endableConversationSpecification->isSatisfiedBy($conversation)) {
                    $conversation->end();
                    $this->conversationRepository->remove($conversation);

                    $conversationalController = $this->conversationContext->getConversationalController();
                    $reflectionConversationalController = $this->conversationContext->getReflectionConversationalController();
                    if ($conversationalController !== null && $reflectionConversationalController !== null) {
                        foreach ($reflectionConversationalController->getStatefulProperties() as $statefulProperty) { /* @var $statefulProperty \ReflectionProperty */
                            if ($conversation->attributes->has($statefulProperty->getName())) {
                                $conversation->attributes->remove($statefulProperty->getName());
                            }
                        }
                    }
                } else {
                    if ($conversation->getCurrentPage()->isEndPage()) {
                        $conversation->logStepCountOnEndPage();
                    }

                    $conversationalController = $this->conversationContext->getConversationalController();
                    $reflectionConversationalController = $this->conversationContext->getReflectionConversationalController();
                    if ($conversationalController !== null && $reflectionConversationalController !== null) {
                        foreach ($reflectionConversationalController->getStatefulProperties() as $statefulProperty) { /* @var $statefulProperty \ReflectionProperty */
                            if (!$statefulProperty->isPublic()) {
                                $statefulProperty->setAccessible(true);
                            }

                            $conversation->attributes->set($statefulProperty->getName(), $statefulProperty->getValue($conversationalController));

                            if (!$statefulProperty->isPublic()) {
                                $statefulProperty->setAccessible(false);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param  \PHPMentors\PageflowerBundle\Pageflow\Pageflow         $pageflow
     * @return \PHPMentors\PageflowerBundle\Conversation\Conversation
     */
    private function createConversation(Pageflow $pageflow)
    {
        return new Conversation($this->generateConversationId(), $this->createPageflow($pageflow));
    }

    /**
     * @param  \PHPMentors\PageflowerBundle\Pageflow\Pageflow $pageflow
     * @return \PHPMentors\PageflowerBundle\Pageflow\Pageflow
     */
    private function createPageflow(Pageflow $pageflow)
    {
        return unserialize(serialize($pageflow));
    }

    /**
     * @return string
     */
    private function generateConversationId()
    {
        return sha1($this->secureRandom->nextBytes(24));
    }
}
