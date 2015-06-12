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

namespace PHPMentors\PageflowerBundle\Form\Extension\Conversation\Type;

use PHPMentors\PageflowerBundle\Conversation\ConversationContext;
use PHPMentors\PageflowerBundle\Conversation\ConversationContextAwareInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class FormTypeConversationExtension extends AbstractTypeExtension implements ConversationContextAwareInterface
{
    /**
     * @var ConversationContext
     */
    private $conversationContext;

    /**
     * @param ConversationContext $conversationContext
     */
    public function setConversationContext(ConversationContext $conversationContext)
    {
        $this->conversationContext = $conversationContext;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $conversation = $this->conversationContext->getConversation();
        if ($conversation !== null) {
            $builder->setAttribute('conversation_factory', $builder->getFormFactory());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function finishView(FormView $view, FormInterface $conversationForm, array $options)
    {
        $conversation = $this->conversationContext->getConversation();
        if ($conversation !== null && !$view->parent && $options['compound']) {
            $conversationForm = $conversationForm->getConfig()->getAttribute('conversation_factory')->createNamed(
                $this->conversationContext->getConversationParameterName(),
                'hidden',
                $conversation->getConversationId(),
                array('mapped' => false)
            );

            $conversationFormView = $conversationForm->createView($view);
            $conversationFormView->vars['full_name'] = $this->conversationContext->getConversationParameterName();
            $view->children[$this->conversationContext->getConversationParameterName()] = $conversationFormView;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
