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

namespace PHPMentors\PageflowerBundle\Controller;

use PHPMentors\DomainKata\Entity\EntityInterface;
use PHPMentors\DomainKata\Repository\RepositoryInterface;

class ReflectionConversationalControllerRepository implements RepositoryInterface
{
    /**
     * @var ReflectionConversationalController[]
     */
    private $entities = array();

    /**
     * {@inheritDoc}
     */
    public function add(EntityInterface $entity)
    {
        assert($entity instanceof ReflectionConversationalController);

        $this->entities[$entity->getClass()->getName()] = $entity;
    }

    /**
     * @param  string                             $class
     * @return ReflectionConversationalController
     */
    public function findByClass($class)
    {
        if (array_key_exists($class, $this->entities)) {
            return $this->entities[$class];
        } else {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove(EntityInterface $entity)
    {
        assert($entity instanceof ReflectionConversationalController);

        if (array_key_exists($entity->getClass()->getName(), $this->entities)) {
            unset($this->entities[$entity->getClass()->getName()]);
        }
    }
}
