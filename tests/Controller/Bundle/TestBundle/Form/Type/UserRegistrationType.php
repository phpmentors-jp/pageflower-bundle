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

namespace PHPMentors\PageflowerBundle\Controller\Bundle\TestBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @since Class available since Release 1.1.0
 */
class UserRegistrationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('lastName', 'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('next', 'Symfony\Component\Form\Extension\Core\Type\SubmitType')
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'user_registration';
    }
}
