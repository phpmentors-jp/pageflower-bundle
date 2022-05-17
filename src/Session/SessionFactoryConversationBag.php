<?php

namespace PHPMentors\PageflowerBundle\Session;

use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionFactoryConversationBag implements SessionFactoryInterface
{
    private $delegate;

    public function __construct(SessionFactoryInterface $delegate)
    {
        $this->delegate = $delegate;

    }

    public function createSession(): SessionInterface
    {
        $session = $this->delegate->createSession();
        $session->registerBag(new ConversationBag('_pageflower_conversations'));
        return $session;
    }
}
